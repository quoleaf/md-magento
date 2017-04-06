Event.observe(window, 'load', function() {
    if (Validation != undefined && Validation.ajaxError != undefined) {
        Validation._oldAjaxError = Validation.ajaxError;
        Validation.ajaxError = function(elm, errorMsg) {
            var name = 'validate-ajax';
            var advice = Validation.getAdvice(name, elm);
            if (advice != null) {
                advice.update(errorMsg);
            }
            Validation._oldAjaxError(elm, errorMsg);
        };
    }
    
    if (varienForm != undefined) {
        varienForm.prototype._validate = function() {
            new Ajax.Request(this.validationUrl,{
                method: 'post',
                parameters: $(this.formId).serialize(true),
                onComplete: this._processValidationResult.bind(this),
                onFailure: this._processFailure.bind(this)
            }); 
        };
    }
})

if (Prototype.Version == '1.6.0.3') {
    Element._purgeElement = function(element) {
        var uid = element._prototypeUID;
        if (uid) {
            Element.stopObserving(element);
            element._prototypeUID = void 0;
            delete Element.Storage[uid];
        }
    }
    
    Element.Storage = {
        UID: 1
    };

    Element.addMethods({
        getStorage: function(element) {
            if (!(element = $(element))) return;

            var uid;
            if (element === window) {
                uid = 0;
            } else {
                if (typeof element._prototypeUID === "undefined")
                    element._prototypeUID = Element.Storage.UID++;
                uid = element._prototypeUID;
            }

            if (!Element.Storage[uid])
                Element.Storage[uid] = $H();

            return Element.Storage[uid];
        },

        store: function(element, key, value) {
            if (!(element = $(element))) return;

            if (arguments.length === 2) {
                Element.getStorage(element).update(key);
            } else {
                Element.getStorage(element).set(key, value);
            }

            return element;
        },

        retrieve: function(element, key, defaultValue) {
            if (!(element = $(element))) return;
            var hash = Element.getStorage(element), value = hash.get(key);

            if (Object.isUndefined(value)) {
                hash.set(key, defaultValue);
                value = defaultValue;
            }

            return value;
        },

        clone: function(element, deep) {
            if (!(element = $(element))) return;
            var clone = element.cloneNode(deep);
            clone._prototypeUID = void 0;
            if (deep) {
                var descendants = Element.select(clone, '*'),
                i = descendants.length;
                while (i--) {
                    descendants[i]._prototypeUID = void 0;
                }
            }
            return Element.extend(clone);
        },

        purge: function(element) {
            if (!(element = $(element))) return;
            var purgeElement = Element._purgeElement;

            purgeElement(element);

            var descendants = element.getElementsByTagName('*'),
            i = descendants.length;

            while (i--) purgeElement(descendants[i]);

            return null;
        }
    });
}