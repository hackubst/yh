(function ($) {

    $.fn.circliful = function (options, callback) {

        //$(this).empty();
		var settings = $.extend({
            // These are the defaults.
            fgcolor: "#556b2f",
            bgcolor: "#eee",
            fill: false,
            width: 15,
            outsize:10,
            dimension: 200,
            fontsize: 15,
            percent: 50,
            animationstep: 1.0,
            iconsize: '20px',
            iconcolor: '#999',
            border: 'default',
            startdegree: 0,
            complete: null
        }, options);

        return this.each(function () {
            var customSettings = ["fgcolor", "bgcolor", "fill", "width","outsize", "dimension", "fontsize", "animationstep","startdegree", "endPercent", "icon", "iconcolor", "iconsize", "border"];
            var customSettingsObj = {};
            var icon = '';
            var endPercent = 0;
            var obj = $(this);
            var fill = false;
            var text, info;
            obj.addClass('circliful');
            obj.empty();

            checkDataAttributes(obj);

            if (obj.data('text') != undefined) {
				obj.data('text',$(obj).attr('data-text'));
                text = obj.data('text');
                if (obj.data('icon') != undefined) {
					obj.data('icon',$(obj).attr('data-icon'));
                    icon = $('<i></i>')
                        .addClass('fa ' + $(this).data('icon'))
                        .css({
                            'color': customSettingsObj.iconcolor,
                            'font-size': customSettingsObj.iconsize
                        });
                }

                if (obj.data('type') != undefined) {
					obj.data('type',$(obj).attr('data-type'));
                    type = $(this).data('type');
                    
                    if (type == 'half') {
                        addCircleText(obj, 'circle-text-half', (customSettingsObj.dimension / 1.45));
                    } else {
                        addCircleText(obj, 'circle-text', customSettingsObj.dimension);
                    }
                } else {
                    addCircleText(obj, 'circle-text', customSettingsObj.dimension);
                }
            }

            if ($(this).data("total") != undefined && $(this).data("part") != undefined) {
                var total = $(this).data("total") / 100;
                obj.data('part',$(obj).attr('data-part'));
				//alert(obj.data('part'));
                percent = (($(this).data("part") / total) / 100).toFixed(3);
                endPercent = ($(this).data("part") / total).toFixed(3)
            } else {
                if ($(this).data("percent") != undefined) {
                    percent = $(this).data("percent") / 100;
                    endPercent = $(this).data("percent")
                } else {
                    percent = settings.percent / 100
                }
            }

            if ($(this).data('info') != undefined) {
				obj.data('info',$(obj).attr('data-info'));
                info = $(this).data('info');

                if ($(this).data('type') != undefined) {
                    type = $(this).data('type');

                    if (type == 'half') {
                        addInfoText(obj, 0.9);
                    } else {
                        addInfoText(obj, 1.25);
                    }
                } else {
                    addInfoText(obj, 1.25);
                }
            }

            $(this).width(customSettingsObj.dimension + 'px');

            var canvas = $('<canvas></canvas>').attr({
			                width: customSettingsObj.dimension,
			                height: customSettingsObj.dimension
			            }).appendTo($(this)).get(0);

            var context = canvas.getContext('2d');
            var x = canvas.width / 2;
            var y = canvas.height / 2;
            var degrees = customSettingsObj.percent * 360.0;
            var radians = degrees * (Math.PI / 180);
            var radius = canvas.width / 2.5;
            var startAngle = 2.3 * Math.PI;
            var endAngle = 0;
            var counterClockwise = false;
            var curPerc = customSettingsObj.animationstep === 0.0 ? endPercent : 0.0;
            var curStep = Math.max(customSettingsObj.animationstep, 0.0);
            var circ = Math.PI * 2;
            var quart = Math.PI / 2;
            var type = '';
            var fireCallback = true;
            var additionalAngelPI = (customSettingsObj.startdegree / 180) * Math.PI;

            if ($(this).data('type') != undefined) {
                type = $(this).data('type');

                if (type == 'half') {
                    startAngle = 2.0 * Math.PI;
                    endAngle = 3.13;
                    circ = Math.PI * 1.0;
                    quart = Math.PI / 0.996;
                }
            }

            /**
             * adds text to circle
             * 
             * @param obj
             * @param cssClass
             * @param lineHeight
             */
            function addCircleText(obj, cssClass, lineHeight) {
                $("<span></span>")
                    .appendTo(obj)
                    .addClass(cssClass)
                    .text(text)
                    .prepend(icon)
                    .css({
                        'line-height': lineHeight + 'px',
                        'font-size': customSettingsObj.fontsize + 'px'
                    });
            }

            /**
             * adds info text to circle
             * 
             * @param obj
             * @param factor
             */
            function addInfoText(obj, factor) {
                $('<span></span>')
                    .appendTo(obj)
					.text(info)
                    .addClass('circle-info-half')
                    .css(
                    'line-height', (customSettingsObj.dimension * factor) + 'px'
                );
            }

            /**
             * checks which data attributes are defined
             * @param obj
             */
            function checkDataAttributes(obj) {
                $.each(customSettings, function (index, attribute) {
                    if (obj.data(attribute) != undefined) {

                        customSettingsObj[attribute] = obj.data(attribute);
						
                    } else {
                        customSettingsObj[attribute] = $(settings).attr(attribute);
                    }

                    if (attribute == 'fill' && obj.data('fill') != undefined) {
                        fill = true;
                    } 
                });
            }

            /**
             * animate foreground circle
             * @param current
             */
            function animate(current) {
                context.clearRect(0, 0, canvas.width, canvas.height);

                context.beginPath();
                context.arc(x, y, radius, endAngle, startAngle, false);

                context.lineWidth = customSettingsObj.width + 1;
                
                context.strokeStyle = customSettingsObj.bgcolor;
                context.stroke();
                
                context.lineWidth = customSettingsObj.outsize;

                if (fill) {
                    context.fillStyle = customSettingsObj.fill;
                    context.fill();
                }

                context.beginPath();
                //context.arc(x, y, radius+1, -(quart), ((circ) * current) - quart, false);
                context.arc(x, y, radius, -(quart) + additionalAngelPI, ((circ) * current) - quart + additionalAngelPI, false);

                if (customSettingsObj.border == 'outline') {
                	context.lineWidth = customSettingsObj.width + 13;
                } else if(customSettingsObj.border == 'inline') {
                	context.lineWidth = customSettingsObj.width - 13;
                } 

                context.strokeStyle = customSettingsObj.fgcolor;
                context.stroke();

                if (curPerc < endPercent) {
                    curPerc += curStep;
					if ( !window.requestAnimationFrame )
					{
						window.requestAnimationFrame = ( function() {

							return window.webkitRequestAnimationFrame ||
							window.mozRequestAnimationFrame ||
							window.oRequestAnimationFrame ||
							window.msRequestAnimationFrame ||
							function( /* function FrameRequestCallback */ callback, /* DOMElement Element */ element ) {

								window.setTimeout( callback, 1000 / 60 );

							};

						} )();
					}
					else
					{
                    requestAnimationFrame(function () {
                        animate(Math.min(curPerc, endPercent) / 100);
                    }, obj);
                }
			}

                if(curPerc == endPercent && fireCallback && typeof(options) != "undefined") {
                	if($.isFunction( options.complete )) {
		            	options.complete();

		            	fireCallback = false;
		            }
                }
            }

            animate(curPerc / 100);

        });
    };
}(jQuery));
