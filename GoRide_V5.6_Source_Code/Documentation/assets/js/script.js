/*!
 * Documenter 2.0
 * http://rxa.li/documenter
 *
 * Copyright 2011, Xaver Birsak
 * http://revaxarts.com
 *
 */
$(document).ready(function() {
	var timeout,
		sections = new Array(),
		sectionscount = 0,
		win = $(window),
		sidebar = $('#documenter_sidebar'),
		nav = $('#documenter_nav'),
		logo = $('#documenter_logo'),
		navanchors = nav.find('a'),
		timeoffset = 50,
		hash = location.hash || null;
		iDeviceNotOS4 = (navigator.userAgent.match(/iphone|ipod|ipad/i) && !navigator.userAgent.match(/OS 5/i)) || false,
		badIE = $('html').prop('class').match(/ie(6|7|8)/)|| false;
	//handle external links (new window)
	$('a[href^=http]').bind('click',function(){
		window.open($(this).attr('href'));
		return false;
	});
	//IE 8 and lower doesn't like the smooth pagescroll
	if(!badIE){
		window.scroll(0,0);
		$('a[href^=#]').bind('click touchstart',function(){
			hash = $(this).attr('href');
			$.scrollTo.window().queue([]).stop();
			goTo(hash);
			return false;
		});
		//if a hash is set => go to it
		if(hash){
			setTimeout(function(){
				goTo(hash);
			},500);
		}
	}
	//We need the position of each section until the full page with all images is loaded
	win.bind('load',function(){
		var sectionselector = 'section';
		//Documentation has subcategories		
		if(nav.find('ol').length){
			sectionselector = 'section, h1[id], h2[id], h3[id], h4[id], h5[id]';
		}
		//saving some information
		$(sectionselector).each(function(i,e){
			var _this = $(this);
			var p = {
				id: this.id,
				pos: _this.offset().top
			};
			sections.push(p);
		});
		//iPhone, iPod and iPad don't trigger the scroll event
		if(iDeviceNotOS4){
			nav.find('a').bind('click',function(){
				setTimeout(function(){
					win.trigger('scroll');				
				},duration);
			});
			//scroll to top
			window.scroll(0,0);
		}
		//how many sections
		sectionscount = sections.length;
		//bind the handler to the scroll event
		win.bind('scroll',function(event){
			clearInterval(timeout);
			//should occur with a delay
			timeout = setTimeout(function(){
				//get the position from the very top in all browsers
				pos = window.pageYOffset || document.documentElement.scrollTop || document.body.scrollTop;
				//iDeviceNotOS4s don't know the fixed property so we fake it
				if(iDeviceNotOS4){
					sidebar.css({height:document.height});
					logo.css({'margin-top':pos});
				}
				//activate Nav element at the current position
				activateNav(pos);
			},timeoffset);
		}).trigger('scroll');
	});
	//the function is called when the hash changes
	function hashchange(){
		goTo(location.hash, false);
	}
	//scroll to a section and set the hash
	function goTo(hash,changehash){
		win.unbind('hashchange', hashchange);
		hash = hash.replace(/!\//,'');
		win.stop().scrollTo(hash,duration,{
			easing:easing,
			axis:'y'			
		});
		if(changehash !== false){
			var l = location;
			location.href = (l.protocol+'//'+l.host+l.pathname+'#'+hash.substr(1));
		}
		win.bind('hashchange', hashchange);
	}
	//activate current nav element
	function activateNav(pos) {
		const offset = 100;
		const scrollBottom = $(window).scrollTop() + $(window).height();
		const docHeight = $(document).height();
		win.unbind('hashchange', hashchange);

		// Clear existing highlights
		navanchors.removeClass('current');
		$('#documenter_nav li').removeClass('active-parent current-child');
		nav.find('ol.submenu').css('display', 'none');

		let foundActive = false;

		for (let i = sectionscount - 1; i >= 0; i--) {
			const section = sections[i];
			const $section = $('#' + section.id);
			if (!$section.length) continue;

			const top = section.pos;
			const bottom = top + $section.outerHeight();

			if ((pos + offset >= top && pos + offset < bottom) || (i === sectionscount - 1 && scrollBottom >= docHeight - 5)) {
				const $anchor = navanchors.filter('[href="#' + section.id + '"]');
				if (!$anchor.length) break;

				$anchor.addClass('current');

				// Highlight breadcrumb path
				let $li = $anchor.closest('li');
				while ($li.length) {
					$li.addClass('active-parent current-child');

					const $parentAnchor = $li.children('a');
					if ($parentAnchor.length && !$parentAnchor.hasClass('current')) {
						$parentAnchor.addClass('current');
					}
					const $submenu = $li.children('ol.submenu');
					if ($submenu.length) {
						$submenu.css('display', 'block');
					}
					$li = $li.parent().closest('li');
				}

				// Optional: Scroll into view
				if ($anchor[0]) {
					$anchor[0].scrollIntoView({ behavior: 'smooth', block: 'nearest' });
				}

				foundActive = true;
				break;
			}
		}

		win.bind('hashchange', hashchange);
	}
    // make code pretty
    window.prettyPrint && prettyPrint();
});