/*

(function($){
    var methods = {
        init: function(options) {
            var settings = $.extend({
                data:   [],
                height: 20,
                maxOptions: 100,
                scrollable: true,
                optionBackground: '#FFFFFF',
                currentBackground: '#EBE1E5'
            }, options);

            var $this = this;
            var data = $this[0].externalData;
            var current = null;
            if (!data) {
                createStorage();
                return $this.each(function() {
                    $this.bind('focus.autocomplete', computeStorage).bind('input.autocomplete', keyHandler).bind('blur.autocomplete', closeStorage);
                });
            }

            function createStorage() {
                var storage = $('<div>');
                $('body').append(storage);
                var position = {left: $this.offset().left, top: $this.offset().top + $this.height()};
                storage.width($this.width() - 2);
                storage.css({
                    'zIndex': 1000,
                    'position': 'absolute',
                    'top': position.top,
                    'left': position.left,
                    'border': '1px solid #ccc',
                    'background': 'white'
                });
                $this.attr('data-autocomplete', 'true');
                storage.on('touchstart.autocomplete', 'div', chooseRecord);
                if (settings.scrollable) {
                    storage.css('overflow', 'auto');
                }
                else {
                    storage.css('overflow', 'hidden')
                }
                if (settings.maxOptions > settings.data.length) {
                    storage.css('max-height', settings.data.length * settings.height + 'px');
                } else {
                    storage.css('max-height', settings.maxOptions * settings.height + 'px');
                }

                $this[0].externalData = {
                    storage:  storage,
                    settings: settings
                };
                storage.hide();
            }
            function closeStorage() {
                var storage = $this[0].externalData.storage;
                storage[0].scrollTop = 0;
                storage.hide();
            }
            function chooseRecord(e) {
                $this.val(e.target.innerHTML);
            }

            function computeStorage() {
                var storage  = $this[0].externalData.storage;
                var settings = $this[0].externalData.settings;
                storage.empty();
                storage.hide();
                var value = $this.val();
                settings.data.sort();
                for (var i = 0; i < settings.data.length; i++) {
                    var string = settings.data[i];
                    if (string.toLowerCase().indexOf(value.toLowerCase()) != -1 && value) {
                        var record = $('<div>').html(settings.data[i]).css('height', settings.height + 'px');
                        storage.append(record);
                    }
                }
                if (storage.find('div').length) {
                    current = storage.find('div:first-child');
                    current.css('background', settings.currentBackground);
                    storage.show();
                }
            }

            function moveUp(scroll) {
                var storage  = $this[0].externalData.storage;
                if (current.prev().length) {
                    if (scroll) {
                        storage[0].scrollTop -= settings.height;
                    }
                    current.css('background', settings.optionBackground);
                    current = current.prev();
                    current.css('background', settings.currentBackground);
                }
            }

            function moveDown(scroll) {
                var storage  = $this[0].externalData.storage;
                if (current.next().length) {
                    if (scroll) {
                        storage[0].scrollTop += settings.height;
                    }
                    current.css('background', settings.optionBackground);
                    current = current.next();
                    current.css('background', settings.currentBackground);
                }
            }

            function keyHandler(e) {
                var storage  = $this[0].externalData.storage;
                var settings = $this[0].externalData.settings;
                switch (e.keyCode) {
                    case 37:
                        break;
                    case 39:
                        break;
                    case 38:
                        var prev = current.index() - 1;
                        if (!settings.scrollable) {
                            moveUp();
                        } else {
                            var scroll = false;
                            if (prev < storage[0].scrollTop / settings.height) {scroll = true;}
                            moveUp(scroll);
                        }
                        break;
                    case 40:
                        var next = current.index() + 1;
                        if (!settings.scrollable) {
                            if (next < settings.maxOptions) {
                                moveDown();
                            }
                        } else {
                            var scroll = false;
                            if (next >= settings.maxOptions) {scroll = true;}
                            moveDown(scroll);
                        }
                        break;
                    case 13:
                        if (storage.width()) {
                            $this.val(current.html());
                            storage.empty();
                            storage.hide();
                        }
                        break;
                    default:
                        computeStorage();
                        break;
                }
            }
        },
        destroy: function() {
            return this.each(function() {
                $this = $(this);
                $this.unbind('.autocomplete');
                var storage = this.externalData.storage;
                $(storage).remove();
                delete this.externalData;
            });
        },
        add: function(field) {
            $this = this;

            function unique(arr) {
                var obj = {};
                for(var i=0; i<arr.length; i++) {
                    var str = arr[i];
                    obj[str] = true;
                }
                return Object.keys(obj);
            }
            if (Object.prototype.toString.call(field) != '[object Array]') {
                field = [field];
            }

            var newArr = $this[0].externalData.settings.data.concat(field);
            newArr = unique(newArr);
            var data = $this[0].externalData;
            data.settings.data = newArr.sort();

            if (data.settings.maxOptions > data.settings.data.length) {
                data.storage.css('max-height', data.settings.data.length * data.settings.height + 'px');
            } else {
                data.storage.css('max-height', data.settings.maxOptions * data.settings.height + 'px');
            }
        }
    };

    $.fn.autoComplete = function(method) {
        if (methods[method]) {
            return methods[method].apply( this, Array.prototype.slice.call( arguments, 1 ));
        } else if ( typeof method === 'object' || ! method ) {
            return methods.init.apply( this, arguments );
        }
    }

})(Zepto);
*/

(function($) {

// event type detect
var eventType = function(e) {
    var touch = $.os.phone || $.os.tablet;
    var evt = {
        click : 'touchend',
        mousedown: 'touchstart',
        mouseup: 'touchend',
        mousemove: 'touchmove',
        mouseover: 'touchstart',
        mouseout: 'touchend',
        mouseenter: 'touchstart',
        mouseleave: 'touchend'
    };
    return touch ? evt[e] : e;
};

var plugin_name = "autocompletion", defaults = {
    caching: true,
    delay: 500,
    postVar: '',
    container: '<ul class="autocompletion"></ul>',
    item: '<li class="autocompletion-item"></li>',
    shim: '<div class="autocompletion-shim"></div>',
    source: []
};
function Plugin(element, options) {
    this.options = $.extend({}, defaults, options);
    this.customize = this.options.customize || this.customize;
    this.fill = this.options.fill || this.fill;
    this.$container = $(this.options.container);
    this.$element = $(element);
    // this.$shim = $(this.options.shim);
    this._attr_value = "data-item-value";
    this._cache = {};
    // this._class_current = "current";
    this._defaults = defaults;
    this._name = plugin_name;
    this.init();
}
Plugin.prototype = {
    init: function() {
        this.bind();
    },
    bind: function() {
        var that = this, item_selector = "[" + this._attr_value + "]";
        this.$element.on("blur", $.proxy(this.blur, this))
        .on("input", function(){setTimeout($.proxy(that.keyup, that), that.options.delay);})
        .on('changes', $.proxy(this.change, this));
        this.$container.on(eventType("mouseenter"), function() {
            that.mousein = true;
        }).on(eventType("mouseleave"), function() {
            that.mousein = false;
        // }).on("mouseenter", item_selector, function(e) {
        // that.$container.find("." + that._class_current).removeClass(that._class_current);
        // $(e.currentTarget).addClass(that._class_current);
        }).on(eventType("click"), item_selector, $.proxy(this.click, this));
    },
    blur: function() {
        // Hide only when cursor outside of the container.
        // This is to ensure that the browser did not hide container before the clue clicked.
        if (!this.mousein) {
            this.hide();
        }
    },
    keyup: function() {
        var source = this.options.source;
        this.q = this.$element.val();
        this.q_lower = this.q.toLowerCase();
        var that = this;
        if (!this.q) {
            return this.hide();
        }
        if (this.options.caching && this._cache[this.q_lower]) {
            // pass to render method directly
            this.render(this._cache[this.q_lower]);
        } else if (typeof(source) === 'string') {
            $.post(source, '' + this.options.postVar + '=' + encodeURIComponent(this.q_lower), function(rs) {
                source = JSON.parse(rs);
                that.suggest(source);
            });
        } else if ($.isFunction(source)) {
            // if it's a function, then run it and pass context
            source(this.q, $.proxy(this.suggest, this));
        } else if($.isArray(source)) {
            this.suggest(source);
        }
    },
    click: function(e) {
        e.stopPropagation();
        e.preventDefault();
        this.select(e);
    },
    change: function(e) {
        this.$element.parents('form')[0].submit();
    },
    suggest: function(items) {
        var that = this,
        filtered_items = $.grep(items, function(item) {
            return item.toLowerCase().indexOf(that.q_lower) !== -1;
        });
        // cache if needed
        if (this.options.caching) {
            this._cache[this.q_lower] = filtered_items;
        }
        this.render(filtered_items);
    },
    render: function(items) {
        if (!items.length) {
            return this.hide();
        }
        var that = this,
            items_dom = $.map(items, function(item) {
                return $(that.options.item).attr(that._attr_value, item).html(that.highlight(item))[0];
            }), position = this.$element.position();
        // render container body
        var css = {
            left: position.left + "px",
            top: position.top + this.$element.height() + "px"
        };
        this.customize(this.$container.css(css).html(items_dom)[0]);
        this.$container.insertAfter(this.$element);
        // this.$shim.css(css).insertAfter(this.$container);
        this.show();
    },
    highlight: function(item) {
        var q = this.q.replace(/[\-\[\]{}()*+?.,\\\^$|#\s]/g, "\\$&");
        return item.replace(new RegExp("(" + q + ")", "ig"), function($1, match) {
            return "<strong>" + match + "</strong>";
        });
    },
    customize: function(clues) {
        return;
    },
    select: function(e) {
        var $el = $(e.target), //this.$container.find("." + this._class_current),
        value = $el.attr(this._attr_value);
        if (value) {
            this.hide();
            this.$element.val(value).trigger('changes');
        }
    },
    fill: function(value) {
        return value;
    },
    show: function() {
        if (!this.visible) {
            this.visible = true;
            this.$container.show();
            // this.$shim.css({
            //     width: this.$container.width(),
            //     height: this.$container.height()
            // }).show();
        }
    },
    hide: function() {
        if (this.visible) {
            this.visible = false;
            this.$container.hide();
            // this.$shim.hide();
        }
    }
};
$.fn[plugin_name] = function(options) {
    return this.each(function() {
        if (!$(this).data("plugin_" + plugin_name)) {
            $(this).data("plugin_" + plugin_name, new Plugin(this, options));
        }
    });
};

})(Zepto);