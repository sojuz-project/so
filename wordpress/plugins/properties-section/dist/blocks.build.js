!function(e){function t(n){if(r[n])return r[n].exports;var o=r[n]={i:n,l:!1,exports:{}};return e[n].call(o.exports,o,o.exports,t),o.l=!0,o.exports}var r={};t.m=e,t.c=r,t.d=function(e,r,n){t.o(e,r)||Object.defineProperty(e,r,{configurable:!1,enumerable:!0,get:n})},t.n=function(e){var r=e&&e.__esModule?function(){return e.default}:function(){return e};return t.d(r,"a",r),r},t.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},t.p="",t(t.s=1)}([function(e,t,r){"use strict";r.d(t,"a",function(){return o});var n=r(6),o=(r.n(n),{attrs:{backgroundColor:{type:"string"}},getAttrs:function(){var e=arguments.length>0&&void 0!==arguments[0]?arguments[0]:{},t=e.setAttributes,r=e.attributes;return r=void 0===r?{}:r,{backgroundColor:r.backgroundColor,setAttributes:t}}}),i=function(e){var t=e.backgroundColor,r=void 0===t?"#FFFFFF":t,o=e.setAttributes,i=void 0===o?function(){return null}:o,l=e.indicatorText,a=void 0===l?"Background color: ":l;return wp.element.createElement("div",null,wp.element.createElement(n.Dropdown,{renderToggle:function(e){var t=e.isOpen,o=e.onToggle;return wp.element.createElement(n.Tooltip,{text:a},wp.element.createElement("button",{type:"button","aria-expanded":t,className:"color-picker-pin",onClick:o},a,wp.element.createElement("span",{style:{backgroundColor:r}},r)))},renderContent:function(){return wp.element.createElement(n.ColorPicker,{color:r,onChangeComplete:function(e){var t=e.hex;return i({backgroundColor:t})},disableAlpha:!0})}}))};t.b=i},function(e,t,r){"use strict";Object.defineProperty(t,"__esModule",{value:!0});r(2)},function(e,t,r){"use strict";var n=r(3),o=(r.n(n),r(4)),i=(r.n(o),r(5)),l=r(0),a=r(7),c=r(8),__=wp.i18n.__;(0,wp.blocks.registerBlockType)("cgb/block-properties-section",{title:__("properties-section"),icon:"shield",category:"common",keywords:[__("properties-section")],attributes:Object.assign({},i.a.attrs,l.a.attrs,a.a.attrs,c.a.attrs),edit:function(e){return wp.element.createElement("div",{className:"properties-block",style:{backgroundColor:e.attributes.backgroundColor,color:e.attributes.textColor}},wp.element.createElement("div",{className:"pickers-group"},wp.element.createElement("div",{className:"block-id"},"Section: ",e.name),wp.element.createElement(a.b,a.a.getAttrs(e)),wp.element.createElement(l.b,l.a.getAttrs(e))),wp.element.createElement(i.b,i.a.getAttrs(e)),wp.element.createElement(c.b,c.a.getAttrs(e)))},save:function(){return wp.element.createElement("div",null)}})},function(e,t){},function(e,t){},function(e,t,r){"use strict";r.d(t,"a",function(){return o});var n=wp.editor.RichText,o={attrs:{title:{type:"string"}},getAttrs:function(){var e=arguments.length>0&&void 0!==arguments[0]?arguments[0]:{},t=e.setAttributes,r=e.attributes;return r=void 0===r?{}:r,{title:r.title,setAttributes:t}}},i=function(e){var t=e.title,r=void 0===t?"":t,o=e.placeholder,i=void 0===o?"":o,l=e.setAttributes,a=void 0===l?function(){return null}:l;return wp.element.createElement(n,{className:"richtext",placeholder:i,value:r,onChange:function(e){return a({title:e})}})};i.defaultProps={placeholder:"Insert section title"},t.b=i},function(e,t){e.exports=wp.components},function(e,t,r){"use strict";r.d(t,"a",function(){return o});var n=r(0),o={attrs:{textColor:{type:"string"}},getAttrs:function(){var e=arguments.length>0&&void 0!==arguments[0]?arguments[0]:{},t=e.setAttributes,r=e.attributes;return r=void 0===r?{}:r,{textColor:r.textColor,setAttributes:t}}},i=function(e){var t=e.textColor,r=e.setAttributes;return wp.element.createElement(n.b,{backgroundColor:t,indicatorText:"Text color: ",setAttributes:function(e){var t=e.backgroundColor;return r({textColor:t})}})};t.b=i},function(e,t,r){"use strict";function n(e){if(Array.isArray(e)){for(var t=0,r=Array(e.length);t<e.length;t++)r[t]=e[t];return r}return Array.from(e)}function o(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}function i(e,t){if(!e)throw new ReferenceError("this hasn't been initialised - super() hasn't been called");return!t||"object"!==typeof t&&"function"!==typeof t?e:t}function l(e,t){if("function"!==typeof t&&null!==t)throw new TypeError("Super expression must either be null or a function, not "+typeof t);e.prototype=Object.create(t&&t.prototype,{constructor:{value:e,enumerable:!1,writable:!0,configurable:!0}}),t&&(Object.setPrototypeOf?Object.setPrototypeOf(e,t):e.__proto__=t)}r.d(t,"a",function(){return b});var a=r(9),c=r.n(a),u=function(){function e(e,t){for(var r=0;r<t.length;r++){var n=t[r];n.enumerable=n.enumerable||!1,n.configurable=!0,"value"in n&&(n.writable=!0),Object.defineProperty(e,n.key,n)}}return function(t,r,n){return r&&e(t.prototype,r),n&&e(t,n),t}}(),s=wp.editor,p=s.RichText,d=s.MediaUpload,m=s.MediaUploadCheck,f=function(){var e=arguments.length>0&&void 0!==arguments[0]?arguments[0]:[],t=arguments[1],r=arguments[2];return e.map(function(e){return e.id===t?Object.assign({},e,r):e})},b={attrs:{richList:{type:"array"}},getAttrs:function(){var e=arguments.length>0&&void 0!==arguments[0]?arguments[0]:{},t=e.setAttributes,r=e.attributes;r=void 0===r?{}:r;var n=r.richList;return{richList:void 0===n?[]:n,setAttributes:t}}},v=function(e){function t(){var e,r,l,a;o(this,t);for(var c=arguments.length,u=Array(c),s=0;s<c;s++)u[s]=arguments[s];return r=l=i(this,(e=t.__proto__||Object.getPrototypeOf(t)).call.apply(e,[this].concat(u))),l.newSlide=function(){var e=Math.floor(9999999999*Math.random()),t=l.props,r=t.richList;(0,t.setAttributes)({richList:[].concat(n(r),[{id:e}])})},l.removeSlide=function(e){return function(){return l.props.setAttributes({richList:l.props.richList.filter(function(t){return t.id!==e})})}},l.updateSlide=function(e,t){return l.props.setAttributes({richList:f(l.props.richList,e,t)})},a=r,i(l,a)}return l(t,e),u(t,[{key:"render",value:function(){var e=this.props,t=e.richList,r=void 0===t?[]:t,n=e.withDescription,o=void 0===n||n,i=this.newSlide,l=this.removeSlide,a=this.updateSlide;return wp.element.createElement("div",{className:"block-group slider"},wp.element.createElement("button",{className:"default-button",onClick:i},"Add list element +"),r.length>0&&r.map(function(){var e=arguments.length>0&&void 0!==arguments[0]?arguments[0]:{};return wp.element.createElement(c.a.Fragment,{key:e.id},wp.element.createElement("div",{className:"rlist-element"},wp.element.createElement("div",null,wp.element.createElement(p,{className:"smalltext",placeholder:"Insert list title",value:e.content,onChange:function(t){return a(e.id,{content:t})}}),o&&wp.element.createElement(p,{className:"smalltext",placeholder:"Insert list description",value:e.description,onChange:function(t){return a(e.id,{description:t})}}),wp.element.createElement("button",{className:"default-button button-red",onClick:l(e.id)},"Remove current list element")),wp.element.createElement("div",{className:"slide-image "+(e.image?"":"image-empty")},e.image&&wp.element.createElement("img",{src:e.image.url,alt:e.alt}),wp.element.createElement(m,null,wp.element.createElement(d,{allowedTypes:["image"],onSelect:function(t){return a(e.id,{image:t})},render:function(t){var r=t.open;return wp.element.createElement("button",{className:"default-button",onClick:r},e.image?"Change image":"Choose image")}})))))}))}}]),t}(c.a.Component);t.b=v},function(e,t){e.exports=React}]);