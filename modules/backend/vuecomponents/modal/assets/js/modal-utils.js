jQuery(function() {
    var ModalUtils = function() {
        var alertClass = Vue.extend(Vue.options.components['backend-component-modal-alert']),
            basicClass = Vue.extend(Vue.options.components['backend-component-modal-basic']),
            confirmClass = Vue.extend(Vue.options.components['backend-component-modal-confirm']);

        function validateAlertOptions(options) {
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
            }

            return options || {};
        }

        function validateBasicArguments(title, text) {
            if (typeof title !== 'string' || !title.length) {
                throw new Error('Modal title is required');
            }

            if (typeof text !== 'string' || !text.length) {
                throw new Error('Modal text is required');
            }
        }

        this.showAlert = function(title, text, options) {
            options = validateAlertOptions(options);
            validateBasicArguments(title, text);

            return new Promise(function(resolve, reject) {
                var alertInstance = new alertClass({
                    propsData: {
                        title: title,
                        text: text,
                        buttonText: options.buttonText,
                        size: options.size || 'normal'
                    }
                });

                alertInstance.$mount();
                document.body.appendChild(alertInstance.$el);

                alertInstance.$once('hook:beforeDestroy', function() {
                    if (document.body.contains(alertInstance.$el)) {
                        document.body.removeChild(alertInstance.$el);
                    }
                    resolve();
                });
            });
        };

        this.showBasic = function (title, content) {
            validateBasicArguments(title, content);

            return new Promise(function (resolve, reject) {
                const basicInstance = new basicClass({
                    propsData: {
                        title: title,
                        text: content,
                        buttonText: ""
                    }
                });
                let buttonClicked = false;

                basicInstance.$mount();
                document.body.appendChild(basicInstance.$el);

                basicInstance.$once('hook:beforeDestroy', function() {
                    if (document.body.contains(basicInstance.$el)) {
                        document.body.removeChild(basicInstance.$el);
                    }

                    if (!buttonClicked) {
                        reject(false);
                    }
                });

                basicInstance.$once('closeclick', function(button) {
                    buttonClicked = true;
                    resolve(button);
                });
            });

        },

        this.showConfirm = function(title, text, options) {
            options = validateAlertOptions(options);
            validateBasicArguments(title, text);

            if (options.isDanger !== undefined && typeof options.isDanger !== 'boolean') {
                throw new Error('showConfirm options.isDanger must be Boolean');
            }

            return new Promise(function(resolve, reject) {
                var confirmInstance = new confirmClass({
                        propsData: {
                            title: title,
                            text: text,
                            isDanger: options.isDanger,
                            buttonText: options.buttonText,
                            size: options.size || 'normal'
                        }
                    }),
                    buttonClicked = false;

                confirmInstance.$mount();
                document.body.appendChild(confirmInstance.$el);

                confirmInstance.$once('hook:beforeDestroy', function() {
                    if (document.body.contains(confirmInstance.$el)) {
                        document.body.removeChild(confirmInstance.$el);
                    }

                    if (!buttonClicked) {
                        reject(false);
                    }
                });

                confirmInstance.$once('buttonclick', function() {
                    buttonClicked = true;
                    resolve();
                });
            });
        };
    };

    if (oc.vueComponentHelpers === undefined) {
        oc.vueComponentHelpers = {};
    }

    oc.vueComponentHelpers.modalUtils = new ModalUtils();
});
