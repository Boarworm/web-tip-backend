/*
 * Inspector autocomplete editor class.
 *
 * Depends on october.autocomplete.js
 */
+function ($) { "use strict";

    var Base = $.oc.inspector.propertyEditors.string,
        BaseProto = Base.prototype;

    var AutocompleteEditor = function(inspector, propertyDefinition, containerCell, group) {
        this.autoUpdateTimeout = null;

        Base.call(this, inspector, propertyDefinition, containerCell, group);
    }

    AutocompleteEditor.prototype = Object.create(BaseProto);
    AutocompleteEditor.prototype.constructor = Base;

    AutocompleteEditor.prototype.dispose = function() {
        this.clearAutoUpdateTimeout();
        this.removeAutocomplete();

        BaseProto.dispose.call(this);
    }

    AutocompleteEditor.prototype.build = function() {
        var container = document.createElement('div'),
            editor = document.createElement('input'),
            placeholder = this.propertyDefinition.placeholder !== undefined ? this.propertyDefinition.placeholder : '',
            value = this.inspector.getPropertyValue(this.propertyDefinition.property);

        editor.setAttribute('type', 'text');
        editor.setAttribute('class', 'string-editor');
        editor.setAttribute('placeholder', placeholder);

        container.setAttribute('class', 'autocomplete-container');

        if (value === undefined) {
            value = this.propertyDefinition.default;
        }

        if (value === undefined) {
            value = '';
        }

        editor.value = value;

        $.oc.foundation.element.addClass(this.containerCell, 'text autocomplete');

        container.appendChild(editor);
        this.containerCell.appendChild(container);

        if (this.propertyDefinition.items !== undefined) {
            this.buildAutoComplete(this.propertyDefinition.items);
        }
        else {
            this.loadDynamicItems();
        }
    }

    AutocompleteEditor.prototype.buildAutoComplete = function(items) {
        var input = this.getInput()

        if (items === undefined) {
            items = [];
        }

        var $input = $(input),
            autocomplete = $input.data('autocomplete')

        if (!autocomplete) {
            $input.autocomplete({
                source: this.prepareItems(items),
                matchWidth: true
            });
        }
        else {
            autocomplete.source = this.prepareItems(items);
        }
    }

    AutocompleteEditor.prototype.removeAutocomplete = function() {
        var input = this.getInput();

        $(input).autocomplete('destroy');
    }

    AutocompleteEditor.prototype.prepareItems = function(items) {
        var result = {};

        if (Array.isArray(items)) {
            for (var i = 0, len = items.length; i < len; i++) {
                result[items[i]] = items[i];
            }
        }
        else {
            result = items;
        }

        return result;
    }

    AutocompleteEditor.prototype.supportsExternalParameterEditor = function() {
        return false;
    }

    AutocompleteEditor.prototype.getContainer = function() {
        return this.getInput().parentNode;
    }

    AutocompleteEditor.prototype.registerHandlers = function() {
        BaseProto.registerHandlers.call(this);

        $(this.getInput()).on('change', this.proxy(this.onInputKeyUp));
    }

    AutocompleteEditor.prototype.unregisterHandlers = function() {
        BaseProto.unregisterHandlers.call(this);

        $(this.getInput()).off('change', this.proxy(this.onInputKeyUp));
    }

    AutocompleteEditor.prototype.saveDependencyValues = function() {
        this.prevDependencyValues = this.getDependencyValues();
    }

    AutocompleteEditor.prototype.getDependencyValues = function() {
        var result = '';

        for (var i = 0, len = this.propertyDefinition.depends.length; i < len; i++) {
            var property = this.propertyDefinition.depends[i],
                value = this.inspector.getPropertyValue(property);

            if (value === undefined) {
                value = '';
            }

            result += property + ':' + value + '-';
        }

        return result;
    }

    AutocompleteEditor.prototype.onInspectorPropertyChanged = function(property) {
        if (!this.propertyDefinition.depends || this.propertyDefinition.depends.indexOf(property) === -1) {
            return;
        }

        this.clearAutoUpdateTimeout();

        if (this.prevDependencyValues === undefined || this.prevDependencyValues != dependencyValues) {
            this.autoUpdateTimeout = setTimeout(this.proxy(this.loadDynamicItems), 200);
        }
    }

    AutocompleteEditor.prototype.clearAutoUpdateTimeout = function() {
        if (this.autoUpdateTimeout !== null) {
            clearTimeout(this.autoUpdateTimeout);
            this.autoUpdateTimeout = null;
        }
    }

    //
    // Dynamic items
    //

    AutocompleteEditor.prototype.showLoadingIndicator = function() {
        $(this.getContainer()).loadIndicator();
    }

    AutocompleteEditor.prototype.hideLoadingIndicator = function() {
        if (this.isDisposed()) {
            return;
        }

        var $container = $(this.getContainer());

        $container.loadIndicator('hide');
        $container.loadIndicator('destroy');

        $container.removeClass('loading-indicator-container');
    }

    AutocompleteEditor.prototype.loadDynamicItems = function() {
        if (this.isDisposed()) {
            return;
        }

        this.clearAutoUpdateTimeout();

        var container = this.getContainer(),
            data = this.getRootSurface().getValues(),
            $form = $(container).closest('form');

        $.oc.foundation.element.addClass(container, 'loading-indicator-container size-small');
        this.showLoadingIndicator();

        if (this.triggerGetItems(data) === false) {
            return;
        }

        data['inspectorProperty'] = this.getPropertyPath();
        data['inspectorClassName'] = this.inspector.options.inspectorClass;

        $form.request(this.inspector.getEventHandler('onInspectableGetOptions'), {
            data: data,
            progressBar: false
        })
        .done(this.proxy(this.itemsRequestDone))
        .always(this.proxy(this.hideLoadingIndicator));
    }

    AutocompleteEditor.prototype.triggerGetItems = function(values) {
        var $inspectable = this.getInspectableElement();
        if (!$inspectable) {
            return true;
        }

        var itemsEvent = $.Event('autocompleteitems.oc.inspector');

        $inspectable.trigger(itemsEvent, [{
            values: values,
            callback: this.proxy(this.itemsRequestDone),
            property: this.inspector.getPropertyPath(this.propertyDefinition.property),
            propertyDefinition: this.propertyDefinition
        }]);

        if (itemsEvent.isDefaultPrevented()) {
            return false;
        }

        return true;
    }

    AutocompleteEditor.prototype.itemsRequestDone = function(data) {
        if (this.isDisposed()) {
            // Handle the case when the asynchronous request finishes after
            // the editor is disposed
            return
        }

        this.hideLoadingIndicator();

        var loadedItems = {};

        if (data.options) {
            for (var i = data.options.length-1; i >= 0; i--) {
                loadedItems[data.options[i].value] = data.options[i].title;
            }
        }

        this.buildAutoComplete(loadedItems);
    }

    $.oc.inspector.propertyEditors.autocomplete = AutocompleteEditor;
}(window.jQuery);
