/*左侧菜单点击*/
$(".side-menu").on('click', 'li a', function(e) {
	var animationSpeed = 300;
	var $this = $(this);
	var checkElement = $this.next();

	if (checkElement.is('.menu-item-child') && checkElement.is(':visible')) {
	  checkElement.slideUp(animationSpeed, function() {
		checkElement.removeClass('menu-open');
	  });
	  checkElement.parent("li").removeClass("active");
	}
	//如果菜单是不可见的
	else if ((checkElement.is('.menu-item-child')) && (!checkElement.is(':visible'))) {
	  //获取上级菜单
	  var parent = $this.parents('ul').first();
	  //从父级开始找所有打开的菜单并关闭
	  var ul = parent.find('ul:visible').slideUp(animationSpeed);
	  //在父级中移出menu-open标记
	  ul.removeClass('menu-open');
	  //获取父级li
	  var parent_li = $this.parent("li");
	  //打开菜单时添加menu-open标记
	  checkElement.slideDown(animationSpeed, function() {
		//添加样式active到父级li
		checkElement.addClass('menu-open');
		parent.find('li.active').removeClass('active');
		parent_li.addClass('active');
	  });
	}
	//防止有链接跳转
	e.preventDefault();

	addIframe($this);
});

$("#HEADER-MENU .layui-header-menu").on('click', function(e) {
	//防止有链接跳转
	e.preventDefault();

	addIframe($(this));
});

/*添加iframe*/
function addIframe(cur){
	var $this = cur;
	var h = $this.attr("href"),
		m = $this.data("index"),
		//m= parseInt($(".layout-main-body").find("iframe.body-iframe").last().data("index"))+1,
		label = $this.find("span").text(),
		isHas = false;
	if (h == "" || $.trim(h).length == 0) {
		return false;
	}
	//console.log($this.data("index"));

	var fullWidth = $(window).width();
	if(fullWidth >= 750){
		$(".layout-side").show();
	}else{
		$(".layout-side").hide();
	}
	
	$(".content-tab").each(function() {
		if ($(this).data("id") == h) {
			if (!$(this).hasClass("active")) {
				$(this).addClass("active").siblings(".content-tab").removeClass("active");
				addTab(this);
			}
			isHas = true;
		}
	});
	if(isHas){
		$(".body-iframe").each(function() {
			if ($(this).data("id") == h) {
				$(this).show().siblings(".body-iframe").hide();
			}
		});
	}
	if (!isHas) {
		var tab = "<a href='javascript:;' class='content-tab active' data-id='"+h+"' data-index='"+m+"'>"+ label +" <i class='icon-font'>&#xe617;</i></a>";
		$(".content-tab").removeClass("active");
		$(".tab-nav-content").append(tab);
		var iframe = "<iframe class='body-iframe' name='iframe"+ m +"' width='100%' height='99%' src='"+ h +"' frameborder='0' data-id='"+ h +"' data-index='"+m+"' seamless></iframe>";
		$(".layout-main-body").find("iframe.body-iframe").hide().parents(".layout-main-body").append(iframe);
		addTab($(".content-tab.active"));
	}
	console.log(m)
	console.log(h)

	$("iframe[name=iframe"+m+"]").attr("src", h).data("id",h);
	return false;
}


/*添加tab*/
function addTab(cur) {
	var prev_all = tabWidth($(cur).prevAll()),
		next_all = tabWidth($(cur).nextAll());
	var other_width =tabWidth($(".layout-main-tab").children().not(".tab-nav"));
	var navWidth = $(".layout-main-tab").outerWidth(true)-other_width;//可视宽度
	var hidewidth = 0;
	if ($(".tab-nav-content").width() < navWidth) {
		hidewidth = 0
	} else {
		if (next_all <= (navWidth - $(cur).outerWidth(true) - $(cur).next().outerWidth(true))) {
			if ((navWidth - $(cur).next().outerWidth(true)) > next_all) {
				hidewidth = prev_all;
				var m = cur;
				while ((hidewidth - $(m).outerWidth()) > ($(".tab-nav-content").outerWidth() - navWidth)) {
					hidewidth -= $(m).prev().outerWidth();
					m = $(m).prev()
				}
			}
		} else {
			if (prev_all > (navWidth - $(cur).outerWidth(true) - $(cur).prev().outerWidth(true))) {
				hidewidth = prev_all - $(cur).prev().outerWidth(true)
			}
		}
	}

	$(".tab-nav-content").animate({
		marginLeft: 0 - hidewidth + "px"
	},
	"fast")
}

/*获取宽度*/
function tabWidth(tabarr) {
	var allwidth = 0;
	$(tabarr).each(function() {
		allwidth += $(this).outerWidth(true)
	});
	return allwidth;
}

/*左按钮事件*/
$(".btn-left").on("click", leftBtnFun);
/*右按钮事件*/
$(".btn-right").on("click", rightBtnFun);
/*选项卡切换事件*/
$(".tab-nav-content").on("click", ".content-tab", navChange);
/*选项卡关闭事件*/
$(".tab-nav-content").on("click", ".content-tab i", closePage2);
/*选项卡双击关闭事件*/
$(".tab-nav-content").on("dblclick", ".content-tab", closePage);


/*左按钮方法*/
function leftBtnFun() {
	var ml = Math.abs(parseInt($(".tab-nav-content").css("margin-left")));
	var other_width = tabWidth($(".layout-main-tab").children().not(".tab-nav"));
	var navWidth = $(".layout-main-tab").outerWidth(true)-other_width;//可视宽度
	var hidewidth = 0;
	if ($(".tab-nav-content").width() < navWidth) {
		return false
	} else {
		var tabIndex = $(".content-tab:first");
		var n = 0;
		while ((n + $(tabIndex).outerWidth(true)) <= ml) {
			n += $(tabIndex).outerWidth(true);
			tabIndex = $(tabIndex).next();
		}
		n = 0;
		if (tabWidth($(tabIndex).prevAll()) > navWidth) {
			while ((n + $(tabIndex).outerWidth(true)) < (navWidth) && tabIndex.length > 0) {
				n += $(tabIndex).outerWidth(true);
				tabIndex = $(tabIndex).prev();
			}
			hidewidth = tabWidth($(tabIndex).prevAll());
		}
	}
	$(".tab-nav-content").animate({
		marginLeft: 0 - hidewidth + "px"
	},
	"fast");
}

/*右按钮方法*/
function rightBtnFun() {
	var ml = Math.abs(parseInt($(".tab-nav-content").css("margin-left")));
	var other_width = tabWidth($(".layout-main-tab").children().not(".tab-nav"));
	var navWidth = $(".layout-main-tab").outerWidth(true)-other_width;//可视宽度
	var hidewidth = 0;
	if ($(".tab-nav-content").width() < navWidth) {
		return false
	} else {
		var tabIndex = $(".content-tab:first");
		var n = 0;
		while ((n + $(tabIndex).outerWidth(true)) <= ml) {
			n += $(tabIndex).outerWidth(true);
			tabIndex = $(tabIndex).next();
		}
		n = 0;
		while ((n + $(tabIndex).outerWidth(true)) < (navWidth) && tabIndex.length > 0) {
			n += $(tabIndex).outerWidth(true);
			tabIndex = $(tabIndex).next()
		}
		hidewidth = tabWidth($(tabIndex).prevAll());
		if (hidewidth > 0) {
			$(".tab-nav-content").animate({
				marginLeft: 0 - hidewidth + "px"
			},
			"fast");
		}
	}
}

/*选项卡切换方法*/
function navChange() {
	if (!$(this).hasClass("active")) {
		var k = $(this).data("id");
		$(".body-iframe").each(function() {
			if ($(this).data("id") == k) {
				$(this).show().siblings(".body-iframe").hide();
				return false
			}
		});
		$(this).addClass("active").siblings(".content-tab").removeClass("active");
		addTab(this);
	}
}

/*选项卡关闭方法*/
function closePage() {
	var url = $(this).data("id");
	var cur_width = $(this).width();
	if ($(this).hasClass("active")) {
		if ($(this).next(".content-tab").size()) {
			var next_url = $(this).next(".content-tab:eq(0)").data("id");
			$(this).next(".content-tab:eq(0)").addClass("active");
			$(".body-iframe").each(function() {
				if ($(this).data("id") == next_url) {
					$(this).show().siblings(".body-iframe").hide();
					return false
				}
			});
			var n = parseInt($(".tab-nav-content").css("margin-left"));
			if (n < 0) {
				$(".tab-nav-content").animate({
						marginLeft: (n + cur_width) + "px"
					},
					"fast")
			}
			$(this).remove();
			$(".body-iframe").each(function() {
				if ($(this).data("id") == url) {
					$(this).remove();
					return false
				}
			})
		}
		if ($(this).prev(".content-tab").size()) {
			var prev_url = $(this).prev(".content-tab:last").data("id");
			$(this).prev(".content-tab:last").addClass("active");
			$(".body-iframe").each(function() {
				if ($(this).data("id") == prev_url) {
					$(this).show().siblings(".body-iframe").hide();
					return false
				}
			});
			$(this).remove();
			$(".body-iframe").each(function() {
				if ($(this).data("id") == url) {
					$(this).remove();
					return false
				}
			})
		}
	} else {
		$(this).remove();
		$(".body-iframe").each(function() {
			if ($(this).data("id") == url) {
				$(this).remove();
				return false
			}
		});
		addTab($(".content-tab.active"))
	}
	return false;
}
function closePage2(){
	var url = $(this).parents(".content-tab").data("id");
	var cur_width = $(this).parents(".content-tab").width();
	if ($(this).parents(".content-tab").hasClass("active")) {
		if ($(this).parents(".content-tab").next(".content-tab").size()) {
			var next_url = $(this).parents(".content-tab").next(".content-tab:eq(0)").data("id");
			$(this).parents(".content-tab").next(".content-tab:eq(0)").addClass("active");
			$(".body-iframe").each(function () {
				if ($(this).data("id") == next_url) {
					$(this).show().siblings(".body-iframe").hide();
					return false
				}
			});
			var n = parseInt($(".tab-nav-content").css("margin-left"));
			if (n < 0) {
				$(".tab-nav-content").animate({
						marginLeft: (n + cur_width) + "px"
					},
					"fast")
			}
			$(this).parents(".content-tab").remove();
			$(".body-iframe").each(function () {
				if ($(this).data("id") == url) {
					$(this).remove();
					return false
				}
			})
		}
		if ($(this).parents(".content-tab").prev(".content-tab").size()) {
			var prev_url = $(this).parents(".content-tab").prev(".content-tab:last").data("id");
			$(this).parents(".content-tab").prev(".content-tab:last").addClass("active");
			$(".body-iframe").each(function () {
				if ($(this).data("id") == prev_url) {
					$(this).show().siblings(".body-iframe").hide();
					return false
				}
			});
			$(this).parents(".content-tab").remove();
			$(".body-iframe").each(function () {
				if ($(this).data("id") == url) {
					$(this).remove();
					return false
				}
			})
		}
	} else {
		$(this).parents(".content-tab").remove();
		$(".body-iframe").each(function () {
			if ($(this).data("id") == url) {
				$(this).remove();
				return false
			}
		});
		addTab($(".content-tab.active"))
	}
	return false;
}

/*循环菜单*/
var globalItemParentShowID = 0;
function initMenu(menu,parent){
	for(var i=0; i<menu.length; i++){
		var item = menu[i];
		if(i == 0 && globalItemParentShowID == 0){
			globalItemParentShowID = item.id;
		}
		var str = "";
		try{
			if(item.isHeader == "1"){
				str = "<li class='menu-header' data-id='"+item.id+"'>"+item.name+"</li>";
				$(parent).append(str);
				if(item.childMenus != ""){
					initMenu(item.childMenus,parent);
				}
			}else{
				item.icon == "" ? item.icon = "&#xe612" : item.icon = item.icon;
				if(item.childMenus == ""){
					if(item.parentId == globalItemParentShowID){
						str = "<li class='item-parent-"+item.parentId+"' style='display: block;'><a href='"+item.url+"' data-index='"+item.id+"'><i class='icon-font'>"+item.icon+"</i><span>"+item.name+"</span></a></li>";
					}else{
						str = "<li class='item-parent-"+item.parentId+"'><a href='"+item.url+"' data-index='"+item.id+"'><i class='icon-font'>"+item.icon+"</i><span>"+item.name+"</span></a></li>";
					}
					$(parent).append(str);
				}else{
					str = "<li><a href='"+item.url+"' data-index='"+item.id+"'><i class='icon-font '>"+item.icon+"</i><span>"+item.name+"</span><i class='icon-font icon-right'>&#xe612;</i></a>";
					str +="<ul class='menu-item-child' id='menu-child-"+item.id+"'></ul></li>";
					$(parent).append(str);
					var childParent = $("#menu-child-"+item.id);
					initMenu(item.childMenus,childParent);
				}
			}
		}catch(e){}
	}
}

/*设置cookie*/
function setCookie(name, value, days){
	if(days == null || days == ''){
		days = 300;
	}
	var exp  = new Date();
	exp.setTime(exp.getTime() + days*24*60*60*1000);
	document.cookie = name + "="+ escape (value) + "; path=/;expires=" + exp.toGMTString();
}

/*获取cookie*/
function getCookie(name) {
	var arr,reg=new RegExp("(^| )"+name+"=([^;]*)(;|$)");
	if(arr = document.cookie.match(reg))
		return unescape(arr[2]); 
	else 
		return null; 
}

/*ajax请求*/
function ajax(url, param, datat, callback) {  
	$.ajax({  
		type: "post",  
		url: url,  
		data: param,  
		dataType: datat,  
		success: function(data){
			callback;
		},  
		error: function () {  
			alert("失败.."); 
		}
	});  
}  


/*头部下拉框移入移出*/
$(document).on("mouseenter",".header-bar-nav",function(){
	$(this).addClass("open");
});
$(document).on("mouseleave",".header-bar-nav",function(){
	$(this).removeClass("open");
});

/*左侧菜单展开和关闭按钮事件*/
$(document).on("click",".layout-side-arrow",function(){
	if($(".layout-side").hasClass("close")){
		$(".layout-side").removeClass("close");
		$(".layout-main").removeClass("full-page");
		$(".layout-footer").removeClass("full-page");
		$(this).removeClass("close");
		$(".layout-side-arrow-icon").removeClass("close");
	}else{
		$(".layout-side").addClass("close");
		$(".layout-main").addClass("full-page");
		$(".layout-footer").addClass("full-page");
		$(this).addClass("close");
		$(".layout-side-arrow-icon").addClass("close");
	}
});

/*头部菜单按钮点击事件*/
$(".header-menu-btn").click(function(){
	$(".layout-side").removeClass("close");
	$(".layout-main").removeClass("full-page");
	$(".layout-footer").removeClass("full-page");
	$(".layout-side-arrow").removeClass("close");
	$(".layout-side-arrow-icon").removeClass("close");
	
	$(".layout-side").slideToggle();
});

/*左侧菜单响应式*/
$(window).resize(function() {  
	var width = $(this).width();  
	if(width >= 750){
		$(".layout-side").show();
	}else{
		$(".layout-side").hide();
	}
});

/*随机颜色*/
function getMathColor(){
	var arr = new Array();
	arr[0] = "#ffac13";
	arr[1] = "#83c44e";
	arr[2] = "#2196f3";
	arr[3] = "#e53935";
	arr[4] = "#00c0a5";
	arr[5] = "#16A085";
	arr[6] = "#ee3768";

	var le = $(".menu-item > a").length;
	for(var i=0;i<le;i++){
		var num = Math.round(Math.random()*5+1);
		var color = arr[num-1];
		$(".menu-item > a").eq(i).find("i:first").css("color",color);
	}
}
