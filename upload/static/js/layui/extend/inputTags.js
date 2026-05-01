/*
* @Author: layui-2
* @Date:   2018-08-31 11:40:42
* @Last Modified by:   hi916
* @Last Modified time: 2021-04-15 15:52:33
* @Last Modified description:添加了flatContent,用于openFlatContent的对象去重;添加了pinArray用于控制标签是否可删除
*/
layui.define(['jquery','layer'],function(exports){
  "use strict";
  var $ = layui.jquery,layer = layui.layer


      //外部接口
      ,inputTags = {
        config: {
          flatContent:[]  //用于多个content的去重
        }

        //设置全局项
        ,set: function(options){
          var that = this;
          that.config = $.extend({}, that.config, options);
          return that;
        }

        // 事件监听
        ,on: function(events, callback){
          return layui.onevent.call(this, MOD_NAME, events, callback)
        }

      }

      //操作当前实例
      ,thisinputTags = function(){
        var that = this
            ,options = that.config;

        return {
          config: options
        }
      }

      //字符常量
      ,MOD_NAME = 'inputTags'


      // 构造器
      ,Class = function(options){
        var that = this;
        that.config = $.extend({}, that.config, inputTags.config, options);
        that.render();
      };

  //默认配置
  Class.prototype.config = {
    close: false  //默认:不开启关闭按钮
    ,theme: ''   //背景:颜色
    ,content: [] //默认标签
    ,pinArray: [] //需要取消删除按钮的元素
    ,openFlatContent:false //是否开启flatContent
    ,aldaBtn: false //默认配置
  };

  // 初始化
  Class.prototype.init = function(){
    var that = this
        ,spans = ''
        ,options = that.config
        ,span = document.createElement("span"),
        spantext = $(span).text("获取全部数据").addClass('albtn');
    if(options.aldaBtn){
      $('body').append(spantext)
    }

    $.each(options.content,function(index,item){
      if(options.openFlatContent&&options.flatContent.indexOf(item) === -1){
        options.flatContent.push(item)
      }
      if (options.pinArray.indexOf(item) === -1){
        spans +='<span><em>'+item+'</em><button type="button" class="close"><svg t="1703172100798" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="3358" width="200" height="200"><path d="M550.848 502.496l308.64-308.896a31.968 31.968 0 1 0-45.248-45.248l-308.608 308.896-308.64-308.928a31.968 31.968 0 1 0-45.248 45.248l308.64 308.896-308.64 308.896a31.968 31.968 0 1 0 45.248 45.248l308.64-308.896 308.608 308.896a31.968 31.968 0 1 0 45.248-45.248l-308.64-308.864z" p-id="3359"></path></svg></button></span>';
      }else {
        spans +='<span><em>'+item+'</em></span>';
      }
      // $('<div class="layui-flow-more"><a href="javascript:;">'+ ELEM_TEXT +'</a></div>');
    })
    options.elem.before(spans)
    that.events()
  }

  Class.prototype.render = function(){
    var that = this
        ,options = that.config;
    options.elem = $(options.elem);
    options.elemSave = $(options.elemSave);
    that.enter()
  };

  // 回车生成标签
  Class.prototype.enter = function(){
    var that = this
        ,spans = ''
        ,options = that.config;
    options.elem.focus(function(){});
    options.elem.keypress(function(event){
      var keynum = (event.keyCode ? event.keyCode : event.which);
      if(keynum == '13'){
        var $val = options.elem.val().trim();
        if(!$val) return false;
        if (options.openFlatContent){
          if (options.content.indexOf($val) === -1){
            if (options.flatContent.indexOf($val) === -1){
              fillTags($val,spans,options,that)
            }
          }
        }else {
          if(options.content.indexOf($val) === -1){
            fillTags($val,spans,options,that)
          }
        }
        options.done && typeof options.done === 'function' && options.done($val);
        options.elem.val('');
        return false;
      }
    })
  };

  var fillTags=function (val,spans,options,that){
    if (options.openFlatContent){
      options.flatContent.push(val);
    }
    options.content.push(val)
    that.render()
    if (options.pinArray.indexOf(val) === -1){
      spans ='<span><em>'+val+'</em><button type="button" class="close"><svg t="1703172100798" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="3358" width="200" height="200"><path d="M550.848 502.496l308.64-308.896a31.968 31.968 0 1 0-45.248-45.248l-308.608 308.896-308.64-308.928a31.968 31.968 0 1 0-45.248 45.248l308.64 308.896-308.64 308.896a31.968 31.968 0 1 0 45.248 45.248l308.64-308.896 308.608 308.896a31.968 31.968 0 1 0 45.248-45.248l-308.64-308.864z" p-id="3359"></path></svg></button></span>';
    }else {
      spans ='<span><em>'+val+'</em></span>';
    }
    options.elem.before(spans);
    var tags = options.content.join(',');
    options.elemSave.val(tags);
    //console.log(options.content);
  }

  //事件处理
  Class.prototype.events = function(){
    var that = this
        ,options = that.config;
    $('.albtn').on('click',function(){
    })
    $(options.elem.parent('.tags')).on('click','.close',function(){
      var Thisremov = $(this).parent('span').remove(),
          ThisText = $(Thisremov).find('em').text();
      options.content.splice($.inArray(ThisText,options.content),1)
      if (options.openFlatContent){
        options.flatContent.splice($.inArray(ThisText,options.flatContent),1)
      }
      //console.log(options.content);
      var tags = options.content.join(',');
      options.elemSave.val(tags);
    })
  };

  //核心入口
  inputTags.render = function(options){
    var inst = new Class(options);
    inst.init();
    return thisinputTags.call(inst);
  };
  exports('inputTags',inputTags);
})