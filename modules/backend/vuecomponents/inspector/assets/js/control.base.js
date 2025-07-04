oc.Modules.register('backend.component.inspector.control.base', function () {
    'use strict';
    var ControlBase = {
        props: {
            control: {
                type: Object,
                required: true
            },
            obj: {
                type: Object,
                required: true
            },
            parentObj: {
                type: Object,
                required: false
            },
            controlId: {
                type: String,
                required: true
            },
            inspectorPreferences: {
                type: Object
            }
        },
        data: function () {
            return {
                loadingDynamicOptions: false
            }
        },
        computed: {
            value: function computeValueProperty() {
                return this.computeValue()
            },

            serverClassName: function computeServerClassName() {
                if (this.control.serverClassName) {
                    return this.control.serverClassName;
                }

                return this.inspectorPreferences.inspectorClass;
            }
        },
        methods: {
            computeValue: function computeValue() {
                var utils = oc.vueComponentHelpers.inspector.utils,
                    result = utils.getProperty(this.obj, this.control.property);

                if (result !== undefined) {
                    return result;
                }

                if (this.control.default !== undefined) {
                    var defaultValue = this.control.default;
                    if (typeof defaultValue === 'object') {
                        defaultValue = $.oc.vueUtils.getCleanObject(defaultValue);
                    }

                    utils.setProperty(this.obj, this.control.property, defaultValue);
                }
                else {
                    var controlDefaultValue = this.getDefaultValue();
                    utils.setProperty(this.obj, this.control.property, controlDefaultValue);
                }

                return utils.getProperty(this.obj, this.control.property);
            },

            validatePropertyValue: function validatePropertyValue() {
                var validatorSet = new oc.vueComponentHelpers.inspector.validatorSet(
                    this.control,
                    this.control.property
                ),
                    result = validatorSet.validate(this.value);

                if (result !== null) {
                    this.$emit('invalid');
                    this.onInvalid();
                }
                else {
                    this.$emit('valid');
                    this.onValid();
                }

                return result;
            },

            markValid: function markValid() {
                this.$emit('valid');
                this.onValid();
            },

            getDefaultValue: function getDefaultValue() {
                return undefined;
            },

            onInvalid: function onInvalid() {
            },

            onValid: function onValid() {
            },

            setManagedValue: function setManagedValue(value) {
                var utils = oc.vueComponentHelpers.inspector.utils;
                utils.setProperty(this.obj, this.control.property, value);
            },

            refreshDisplayedValue: function refreshDisplayedValue() { },

            focusControl: function focusControl() { },

            inspectorGetTab: function inspectorGetTab() {
                return this.control.tab;
            },

            loadDynamicOptions: function loadDynamicOptions() {
                const dataLoader = oc.Modules.import('backend.component.inspector.dataloader');

                var data = Object.assign({}, $.oc.vueUtils.getCleanObject(this.parentObj), $.oc.vueUtils.getCleanObject(this.obj));
                if (typeof this.control.getDynamicOptionsExtraData === 'function') {
                    const extraData = this.control.getDynamicOptionsExtraData();
                    if (typeof extraData !== 'object') {
                        throw new Error('getDynamicOptionsExtraData must return an object');
                    }

                    Object.assign(data, extraData);
                }

                this.loadingDynamicOptions = true;

                data['inspectorProperty'] = this.control.property;
                data['inspectorClassName'] = this.serverClassName;

                var handlerAlias = this.inspectorPreferences.handlerAlias,
                    optionsHandler = handlerAlias
                        ? handlerAlias + '::onInspectableGetOptions'
                        : 'onInspectableGetOptions';

                return dataLoader.requestOptions(
                        this.$el,
                        this.serverClassName,
                        optionsHandler,
                        data,
                        this.control.dataCacheKeyName,
                        this.control.dataCacheKeyPropertyNames
                    )
                    .then(data => this.dynamicOptionsLoaded(data))
                    .finally(() => this.loadingDynamicOptions = false);
            },

            dynamicOptionsLoaded: function dynamicOptionsLoaded(data) {}
        }
    };

    if (oc.vueComponentHelpers === undefined) {
        oc.vueComponentHelpers = {};
    }

    if (oc.vueComponentHelpers === undefined) {
        oc.vueComponentHelpers.inspector = {};
    }

    oc.vueComponentHelpers.inspector.controlBase = ControlBase;
});
