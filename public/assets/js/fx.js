(function($) {
    var fx = {
        "initModalWindow": function() {
            if ($(".modal-window").length == 0) {
                var modal_close = $("<a>")
                        .attr("href", "#")
                        .addClass("modal-window-close")
                        .css({"display": "inline"})
                        .click(function(event) {
                            fx.boxout(event);
                        });
                var modal_window_content = $("<div>").addClass("modal-window-content");
                return $("<div>")
                        .hide()
                        .addClass("modal-window")
                        .append(modal_close)
                        .append(modal_window_content)
                        .appendTo("body");
            }
            else {
                return $(".modal-window");
            }
        },
        "initModalOverlay": function() {
            var overlayHeight = $(document).height();
            var overlayWidth = $(window).width();
            return $("<div>")
                    .hide()
                    .addClass("modal-overlay")
                    .css({"width": overlayWidth, "height": overlayHeight})
                    .click(function(event) {
                        fx.boxout(event);
                    })
                    .appendTo("body");
        },
        "boxin": function(data, modalWindow, modalOverlay) {
            var leftFix = $(window).width() / 2 - modalWindow.width() / 2;
            modalWindow
                    .hide()
                    .css({'left': leftFix})
                    .find(".modal-window-content")
                    .append(data);
            $(modalOverlay).css({"background-color": "#000"}).fadeTo("slow", 0.7);
            $(modalWindow).fadeIn("slow");
        },
        "boxout": function(event) {
            if (event != undefined) {
                event.preventDefault();
            }
            $("a").removeClass("active");
            $(".modal-window,.modal-overlay").fadeOut("slow", function() {
                $('.modal-window-content').children().remove();
            });
        },
        "deserialize": function(str) {
            var data = str.split("&"),
                // Declares variables for use in the loop
                pairs = [], entry = {}, key, val;

            // Loops through each name-value pair
            for (x in data) {
                // Splits each pair into an array
                pairs = data[x].split("=");
                // The first element is the name
                key = pairs[0];
                // Second element is the value
                val = pairs[1];
                // Stores each value as an object property
                entry[key] = fx.urldecode(val);
            }
            return entry;
        },
        "urldecode": function(str) {
            // Converts plus signs to spaces
            var converted = str.replace(/\+/g, ' ');
            // Converts any encoded entities back
            return decodeURIComponent(converted);
        }
    };

    $.fx.initModalWindow = function() {
        return  fx.initModalWindow();
    };

    $.fx.initModalOverlay = function() {
        return fx.initModalOverlay();
    };

    $.fx.boxin = function(data, modalWindow, modalOverlay) {
        return  fx.boxin(data, modalWindow, modalOverlay);
    };

    $.fx.boxout = function(event) {
        return  fx.boxout(event);
    };

    $.fx.deserialize = function(str) {
        return fx.deserialize(str);
    };

    $.fx.urldecode = function(str) {
        return fx.urldecode(str);
    };
})(jQuery)