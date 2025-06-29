oc.Modules.register('backend.component.inspector.inspectorhost', function () {
    var InspectorHost = function () {
        this.showModal = function showModal(title, obj, dataSchema, uniqueId, options, parentObj) {
            if (typeof title !== 'string' || !title.length) {
                throw new Error('Inspector title is a required string');
            }

            if (typeof obj !== 'object') {
                throw new Error('Inspector Object must be an object');
            }

            if (parentObj !== undefined && typeof parentObj !== 'object') {
                throw new Error('Inspector Parent Object must be an object');
            }

            if (!Array.isArray(dataSchema)) {
                throw new Error('Inspector data schema must be an array');
            }

            if (typeof uniqueId !== 'string' || !uniqueId.length) {
                throw new Error('Inspector unique key is a required string');
            }

            if (options) {
                if (typeof options !== 'object') {
                    throw new Error('options must be an object');
                }

                if (options.buttonText && typeof options.buttonText !== 'string') {
                    throw new Error('options.buttonText must be a string');
                }

                if (options.size && typeof options.size !== 'string') {
                    throw new Error('options.size must be a string');
                }

                if (options.description && typeof options.description !== 'string') {
                    throw new Error('options.description must be a string');
                }

                if (options.resizableWidth && typeof options.resizableWidth !== 'boolean') {
                    throw new Error('options.resizableWidth must be boolean');
                }

                if (options.beforeApplyCallback && typeof options.beforeApplyCallback !== 'function') {
                    throw new Error('options.beforeApplyCallback must be a function');
                }

                if (options.liveMode && typeof options.liveMode !== 'boolean') {
                    throw new Error('options.liveMode must be boolean');
                }
            }

            options = options || {};

            var modalClass = Vue.extend(Vue.options.components['backend-component-inspector-host-modal']);

            return new Promise(function (resolve, reject) {
                var inspectorInstance = new modalClass({
                        propsData: {
                            title: title,
                            description: options.description || '',
                            dataSchema: dataSchema,
                            data: {
                                obj: obj,
                                parentObj: parentObj
                            },
                            buttonText: options.buttonText,
                            size: options.size || 'normal',
                            uniqueId: uniqueId,
                            handlerAlias: options.handlerAlias,
                            resizableWidth: options.resizableWidth,
                            liveMode: options.liveMode
                        }
                    }),
                    applyClicked = false;

                inspectorInstance.$mount();
                document.body.appendChild(inspectorInstance.$el);

                inspectorInstance.$once('hook:beforeDestroy', function () {
                    document.body.removeChild(inspectorInstance.$el);

                    if (!applyClicked) {
                        reject();
                    }
                });

                inspectorInstance.$on('beforeapply', function (callbackHolder) {
                    if (options.beforeApplyCallback) {
                        callbackHolder.callback = options.beforeApplyCallback;
                    }
                });

                inspectorInstance.$once('applyclick', function () {
                    applyClicked = true;
                    resolve();
                });
            });
        };
    };

    if (oc.vueComponentHelpers === undefined) {
        oc.vueComponentHelpers = {};
    }

    if (oc.vueComponentHelpers.inspector === undefined) {
        oc.vueComponentHelpers.inspector = {};
    }

    oc.vueComponentHelpers.inspector.host = new InspectorHost();
});