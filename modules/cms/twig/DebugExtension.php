<?php namespace Cms\Twig;

use System;
use Cms\Classes\Controller;
use Cms\Classes\ComponentBase;
use October\Rain\Database\Model;
use October\Rain\Support\Debug\HtmlDumper;
use Twig\Template as TwigTemplate;
use Twig\Environment as TwigEnvironment;
use Twig\TwigFunction as TwigSimpleFunction;
use Twig\Extension\AbstractExtension as TwigExtension;
use Symfony\Component\VarDumper\Cloner\VarCloner;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use ReflectionClass;

/**
 * DebugExtension for Twig
 *
 * @package october\cms
 * @author Alexey Bobkov, Samuel Georges
 */
class DebugExtension extends TwigExtension
{
    const PAGE_CAPTION = 'Page variables';
    const ARRAY_CAPTION = 'Array variables';
    const OBJECT_CAPTION = 'Object variables';
    const COMPONENT_CAPTION = 'Component variables';

    /**
     * @var \Cms\Classes\Controller controller reference.
     */
    protected $controller;

    /**
     * @var integer zebra is a helper for rendering table row styles.
     */
    protected $zebra = 1;

    /**
     * @var boolean variablePrefix is true if no variable is passed.
     */
    protected $variablePrefix = false;

    /**
     * @var array commentMap of method/property comments.
     */
    protected $commentMap = [];

    /**
     * @var array blockMethods that should not be included in the dump.
     */
    protected $blockMethods = [
        'componentDetails',
        'defineProperties',
        'getPropertyOptions',
        'offsetExists',
        'offsetGet',
        'offsetSet',
        'offsetUnset'
    ];

    /**
     * __construct the extension instance.
     */
    public function __construct(?Controller $controller = null)
    {
        $this->controller = $controller;
    }

    /**
     * addExtensionToTwig adds this extension to the Twig environment and ensures
     * it cannot be used in safe mode.
     */
    public static function addExtensionToTwig(TwigEnvironment $twig)
    {
        if (System::checkSafeMode()) {
            return;
        }

        $twig->addExtension(new static);
    }

    /**
     * getFunctions returns a list of global functions to add to the existing list.
     * @return array An array of global functions
     */
    public function getFunctions()
    {
        return [
            new TwigSimpleFunction('dump', [$this, 'runDump'], [
                'is_safe' => ['html'],
                'needs_context' => true,
                'needs_environment' => true
            ]),
            new TwigSimpleFunction('d', [$this, 'dumpVars'], [
                'is_safe' => ['html'],
            ]),
            new TwigSimpleFunction('dd', [$this, 'dumpVarsAndDie'], [
                'is_safe' => ['html'],
            ]),
        ];
    }

    /**
     * dumpVars returns dumped variables
     */
    public function dumpVars(...$vars)
    {
        return implode(PHP_EOL, array_map(function($var) {
            return $this->variableToDump($var);
        }, $vars));
    }

    /**
     * dumpVarsAndDie outputs dumped variables and then dies
     */
    public function dumpVarsAndDie(...$vars)
    {
        if (!in_array(\PHP_SAPI, ['cli', 'phpdbg'], true) && !headers_sent()) {
            header('HTTP/1.1 500 Internal Server Error');
        }

        echo $this->dumpVars(...$vars);
        exit(1);
    }

    /**
     * runDump processes the dump variables, if none is supplied, all the twig
     * template variables are used
     * @param  TwigEnvironment $env
     * @param  array $context
     * @return string
     */
    public function runDump(TwigEnvironment $env, $context)
    {
        if (!$env->isDebug()) {
            return;
        }

        $result = '';

        $count = func_num_args();
        if ($count == 2) {
            $this->variablePrefix = true;
            $vars = [];
            foreach ($context as $key => $value) {
                if (!$value instanceof TwigTemplate) {
                    $vars[$key] = $value;
                }
            }

            $result .= $this->dump($vars, static::PAGE_CAPTION);
        }
        else {
            $this->variablePrefix = false;
            for ($i = 2; $i < $count; $i++) {
                $var = func_get_arg($i);

                if ($var instanceof ComponentBase) {
                    $caption = [static::COMPONENT_CAPTION, get_class($var)];
                }
                elseif (is_array($var)) {
                    $caption = static::ARRAY_CAPTION;
                }
                elseif (is_object($var)) {
                    $caption = [static::OBJECT_CAPTION, get_class($var)];
                }
                else {
                    $caption = [static::OBJECT_CAPTION, gettype($var)];
                }

                $result .= $this->dump($var, $caption);
            }
        }
        return $result;
    }

    /**
     * dump information about a variable
     * @param mixed $variables Variable to dump
     * @param mixed $caption Caption [and subcaption] of the dump
     * @return void
     */
    public function dump($variables = null, $caption = null)
    {
        $this->commentMap = [];
        $this->zebra = 1;
        $info = [];

        if (!is_array($variables)) {
            if ($variables instanceof Paginator) {
                $variables = $this->paginatorToArray($variables);
            }
            elseif (is_object($variables)) {
                $variables = $this->objectToArray($variables);
            }
            else {
                $variables = [$variables];
            }
        }

        $output = [];
        $output[] = '<table>';

        if ($caption) {
            $output[] = $this->makeTableHeader($caption);
        }

        $output[] = '<tbody>';
        foreach ($variables as $key => $item) {
            $output[] = $this->makeTableRow($key, $item);
        }
        $output[] = '</tbody>';
        $output[] = '</table>';

        $html = implode(PHP_EOL, $output);

        return '<pre class="twig-dump-container" style="' . $this->getContainerCss() . '">' . $html . '</pre>';
    }

    /**
     * makeTableHeader builds the HTML used for the table header.
     * @param mixed $caption Caption [and subcaption] of the dump
     * @return string
     */
    protected function makeTableHeader($caption)
    {
        if (is_array($caption)) {
            [$caption, $subcaption] = $caption;
        }

        $output = [];
        $output[] = '<thead>';
        $output[] = '<tr>';
        $output[] = '<th colspan="3" style="'.$this->getHeaderCss().'">';
        $output[] = $caption;

        if (isset($subcaption)) {
            $output[] = '<div style="'.$this->getSubheaderCss().'">'.$subcaption.'</div>';
        }

        $output[] = '</th>';
        $output[] = '</tr>';
        $output[] = '</thead>';
        return implode(PHP_EOL, $output);
    }

    /**
     * makeTableRow builds the HTML used for each table row.
     * @param  mixed $key
     * @param  mixed $variable
     * @return string
     */
    protected function makeTableRow($key, $variable)
    {
        $this->zebra = $this->zebra ? 0 : 1;
        $css = $this->getDataCss($variable);
        $output = [];
        $output[] = '<tr>';
        $output[] = '<td style="'.$css.';cursor:pointer" onclick="'.$this->evalToggleDumpOnClick().'">'.$this->evalKeyLabel($key).'</td>';
        $output[] = '<td style="'.$css.'">'.$this->evalVarLabel($variable).'</td>';
        $output[] = '<td style="'.$css.'">'.$this->evalVarDesc($variable, $key).'</td>';
        $output[] = '</tr>';
        $output[] = '<tr>';
        $output[] = '<td colspan="3">'.$this->evalVarDump($variable).'</td>';
        $output[] = '</tr>';
        return implode(PHP_EOL, $output);
    }

    /**
     * evalToggleDumpOnClick builds JavaScript for toggling the dump container
     * @return string
     */
    protected function evalToggleDumpOnClick()
    {
        $output = "var d=this.parentElement.nextElementSibling.getElementsByTagName('div')[0];";
        $output .= "d.style.display=='none'?d.style.display='block':d.style.display='none'";
        return $output;
    }

    /**
     * evalVarDump dumps a variable using HTML Dumper, wrapped in a hidden DIV element.
     * @param  mixed $variable
     * @return string
     */
    protected function evalVarDump($variable)
    {
        $output = '<div style="display:none">';
        $output .= $this->variableToDump($variable);
        $output .= '</div>';
        return $output;
    }

    /**
     * evalKeyLabel returns a variable name as HTML friendly.
     * @param  string $key
     * @return string
     */
    protected function evalKeyLabel($key)
    {
        if ($this->variablePrefix === true) {
            $output = '{{ <span>%s</span> }}';
        }
        elseif (is_array($this->variablePrefix)) {
            $prefix = implode('.', $this->variablePrefix);
            $output = '{{ <span>'.$prefix.'.%s</span> }}';
        }
        elseif ($this->variablePrefix) {
            $output = '{{ <span>'.$this->variablePrefix.'.%s</span> }}';
        }
        else {
            $output = '%s';
        }

        return sprintf($output, $key);
    }

    /**
     * evalVarLabel evaluates the variable description
     * @param  mixed $variable
     * @return string
     */
    protected function evalVarLabel($variable)
    {
        $type = $this->getType($variable);
        switch ($type) {
            case 'object':
                return $this->evalObjLabel($variable);

            case 'array':
                return $type . '('.count($variable).')';

            default:
                return $type;
        }
    }

    /**
     * getType evaluates an object type for label
     * @param  mixed $variable
     * @return string
     */
    protected function getType($variable)
    {
        $type = gettype($variable);
        if ($type === 'string' && substr($variable, 0, 12) === '___METHOD___') {
            return 'method';
        }

        return $type;
    }

    /**
     * evalObjLabel evaluates an object type for label
     * @param  object $variable
     * @return string
     */
    protected function evalObjLabel($variable)
    {
        $class = get_class($variable);
        $label = class_basename($variable);

        if ($variable instanceof ComponentBase) {
            $label = '<strong>Component</strong>';
        }
        elseif ($variable instanceof Collection) {
            $label = 'Collection('.$variable->count().')';
        }
        elseif ($variable instanceof Paginator) {
            $label = 'Paged Collection('.$variable->count().')';
        }
        elseif ($variable instanceof Model) {
            $label = 'Model';
        }

        return '<abbr title="'.e($class).'">'.$label.'</abbr>';
    }

    /**
     * evalVarDesc evaluates the variable description
     * @param  mixed $variable
     * @return string
     */
    protected function evalVarDesc($variable, $key)
    {
        $type = $this->getType($variable);

        if ($type == 'method') {
            return $this->evalMethodDesc($variable);
        }

        if (isset($this->commentMap[$key])) {
            return $this->commentMap[$key];
        }

        if ($type == 'array') {
            return $this->evalArrDesc($variable);
        }

        if ($type == 'object') {
            return $this->evalObjDesc($variable);
        }

        return '';
    }

    /**
     * evalMethodDesc evaluates an method type for description
     * @param  string $variable
     * @return string
     */
    protected function evalMethodDesc($variable)
    {
        $parts = explode('|', $variable);
        if (count($parts) < 2) {
            return null;
        }

        $method = $parts[1];
        return $this->commentMap[$method] ?? null;
    }

    /**
     * evalArrDesc evaluates an array type for description
     * @param  array $variable
     * @return string
     */
    protected function evalArrDesc($variable)
    {
        $output = [];
        foreach ($variable as $key => $value) {
            $output[] = '<abbr title="'.e(gettype($value)).'">'.$key.'</abbr>';
        }

        return implode(', ', $output);
    }

    /**
     * evalObjDesc evaluates an object type for description
     * @param  array $variable
     * @return string
     */
    protected function evalObjDesc($variable)
    {
        $output = [];
        if ($variable instanceof ComponentBase) {
            $details = $variable->componentDetails();
            $output[] = '<abbr title="'.array_get($details, 'description').'">';
            $output[] = array_get($details, 'name');
            $output[] = '</abbr>';
        }

        return implode('', $output);
    }

    //
    // Object helpers
    //

    /**
     * paginatorToArray returns default comment information for a paginator object.
     * @param  Illuminate\Pagination\Paginator $paginator
     * @return array
     */
    protected function paginatorToArray(Paginator $paginator)
    {
        $this->commentMap = [
            'links()' => 'Renders links for navigating the collection',
            'currentPage' => 'Get the current page for the request.',
            'lastPage' => 'Get the last page that should be available.',
            'perPage' => 'Get the number of items to be displayed per page.',
            'total' => 'Get the total number of items in the complete collection.',
            'from' => 'Get the number of the first item on the paginator.',
            'to' => 'Get the number of the last item on the paginator.',
            'count' => 'Returns the number of items in this collection',
        ];

        return [
            'links' => '___METHOD___|links()',
            'currentPage' => '___METHOD___|currentPage',
            'lastPage' => '___METHOD___|lastPage',
            'perPage' => '___METHOD___|perPage',
            'total' => '___METHOD___|total',
            'from' => '___METHOD___|from',
            'to' => '___METHOD___|to',
            'count' => '___METHOD___|count',
        ];
    }

    /**
     * objectToArray returns a map of an object as an array, containing methods and properties.
     * @param  mixed $object
     * @return array
     */
    protected function objectToArray($object)
    {
        $class = get_class($object);
        $info = new ReflectionClass($object);

        $this->commentMap[$class] = [];

        $methods = [];
        foreach ($info->getMethods() as $method) {
            // Only public
            if (!$method->isPublic()) {
                continue;
            }
            // Only locals
            if ($method->class != $class) {
                continue;
            }
            $name = $method->getName();
            // Blocked methods
            if (in_array($name, $this->blockMethods)) {
                continue;
            }
            // AJAX methods
            if (preg_match('/^on[A-Z]{1}[\w+]*$/', $name)) {
                continue;
            }
            // getSomethingOptions
            if (preg_match('/^get[A-Z]{1}[\w+]*Options$/', $name)) {
                continue;
            }
            // Magic/hidden method
            if (substr($name, 0, 1) == '_') {
                continue;
            }
            $name .= '()';
            $methods[$name] = '___METHOD___|'.$name;
            $this->commentMap[$name] = $this->evalDocBlock($method);
        }

        $vars = [];
        foreach ($info->getProperties() as $property) {
            // Only non-static
            if ($property->isStatic()) {
                continue;
            }
            // Only public
            if (!$property->isPublic()) {
                continue;
            }
            // Only locals
            if ($property->class != $class) {
                continue;
            }
            $name = $property->getName();
            $vars[$name] = $object->{$name};
            $this->commentMap[$name] = $this->evalDocBlock($property);
        }

        return $methods + $vars;
    }

    /**
     * evalDocBlock extracts the comment from a DocBlock
     * @param  ReflectionClass $reflectionObj
     * @return string
     */
    protected function evalDocBlock($reflectionObj)
    {
        $comment = $reflectionObj->getDocComment();
        $comment = substr($comment, 3, -2);

        $parts = explode('@', $comment);
        $comment = array_shift($parts);
        $comment = trim(trim($comment), '*');
        $comment = implode(' ', array_map('trim', explode('*', $comment)));

        return $comment;
    }

    //
    // Style helpers
    //

    /**
     * getDataCss gets the CSS string for the output data
     * @param  mixed $variable
     * @return string
     */
    protected function getDataCss($variable)
    {
        $css = [
            'padding' => '7px',
            'background-color' => $this->zebra ? '#D8D9DB' : '#FFF',
            'color' => '#405261',
        ];

        $type = gettype($variable);
        if ($type == 'NULL') {
            $css['color'] = '#999';
        }

        return $this->arrayToCss($css);
    }

    /**
     * getContainerCss gets the CSS string for the output container
     * @return string
     */
    protected function getContainerCss()
    {
        return $this->arrayToCss([
            'background-color' => '#F3F3F3',
            'border' => '1px solid #bbb',
            'border-radius' => '4px',
            'font-size' => '12px',
            'line-height' => '18px',
            'margin' => '20px',
            'padding' => '7px',
            'display' => 'inline-block',
        ]);
    }

    /**
     * getHeaderCss gets the CSS string for the output header
     * @return string
     */
    protected function getHeaderCss()
    {
        return $this->arrayToCss([
            'font-size' => '18px',
            'font-weight' => 'normal',
            'margin' => '0',
            'padding' => '10px',
            'background-color' => '#7B8892',
            'color' => '#FFF',
        ]);
    }

    /**
     * getSubheaderCss gets the CSS string for the output subheader
     * @return string
     */
    protected function getSubheaderCss()
    {
        return $this->arrayToCss([
            'font-size' => '12px',
            'font-weight' => 'normal',
            'font-style' => 'italic',
            'margin' => '0',
            'padding' => '0',
            'background-color' => '#7B8892',
            'color' => '#FFF',
        ]);
    }

    /**
     * arrayToCss converts a key/value pair array into a CSS string
     * @param array $rules List of rules to process
     * @return string
     */
    protected function arrayToCss(array $rules)
    {
        $strings = [];

        foreach ($rules as $key => $value) {
            $strings[] = $key . ': ' . $value;
        }

        return implode('; ', $strings);
    }

    /**
     * variableToDump converts a variable to a HTML dumped version
     */
    protected function variableToDump($variable)
    {
        $dumper = new HtmlDumper;
        $cloner = new VarCloner;

        return $dumper->dump($cloner->cloneVar($variable), true);
    }
}
