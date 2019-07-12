!function(e){function t(r){if(n[r])return n[r].exports;var a=n[r]={i:r,l:!1,exports:{}};return e[r].call(a.exports,a,a.exports,t),a.l=!0,a.exports}var n={};t.m=e,t.c=n,t.d=function(e,n,r){t.o(e,n)||Object.defineProperty(e,n,{configurable:!1,enumerable:!0,get:r})},t.n=function(e){var n=e&&e.__esModule?function(){return e.default}:function(){return e};return t.d(n,"a",n),n},t.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},t.p="",t(t.s=0)}([function(e,t,n){"use strict";Object.defineProperty(t,"__esModule",{value:!0});n(1)},function(e,t,n){"use strict";var r=n(2),a=(n.n(r),n(3)),l=n(5),o=n(6),i=Object.assign||function(e){for(var t=1;t<arguments.length;t++){var n=arguments[t];for(var r in n)Object.prototype.hasOwnProperty.call(n,r)&&(e[r]=n[r])}return e},c=Object(a.a)("textColor"),u=c.Comp,s=c.config,m=Object(a.a)("excerptColor","#000"),p=m.Comp,b=m.config,d=Object(o.a)("titleAlign"),g=d.Comp,v=d.config,f=Object(o.a)("excerptAlign"),w=f.Comp,C=f.config,A=Object(o.a)("contentAlign"),E=A.Comp,x=A.config,k=Object(o.a)("imageSize"),h=k.Comp,y=k.config,__=wp.i18n.__,N=wp.blocks.registerBlockType,j=wp.editor.RichText;N("sojuz/block-content-section",{title:__("content-section"),icon:"shield",category:"common",keywords:[__("content-section")],attributes:Object.assign({blockTitle:"",title:{type:"string"},content:{type:"string"},excerpt:{type:"string"}},l.a.attrs,b.attrs,a.b.attrs,s.attrs,o.b.attrs,v.attrs,x.attrs,C.attrs,y.attrs),edit:function(e){return wp.element.createElement("div",{className:"sojuz-block content-block "+o.b.getAttrs(e).gridCss,style:{backgroundColor:e.attributes.backgroundColor,color:e.attributes.textColor}},wp.element.createElement("div",{className:"main-pickers-group"},wp.element.createElement("div",{className:"block-id"},"Section: ",e.name),wp.element.createElement(u,i({},s.getAttrs(e),{indicatorText:"Text: ",textColor:e.attributes.textColor})),wp.element.createElement(a.c,a.b.getAttrs(e)),wp.element.createElement(o.c,i({},o.b.getAttrs(e),{data:[{value:"default",label:"Default"},{value:"default-revers",label:"Default rev."},{value:"horizontal",label:"Horizontal"},{value:"horizontal-revers",label:"Horizontal rev."},{value:"compact",label:"Compact"},{value:"compact-revers",label:"Compact rev."}]}))),wp.element.createElement("div",{className:"block-group title"},wp.element.createElement("div",{className:"extend-pickers-group"},wp.element.createElement("div",{className:"group-title"},"Title"),wp.element.createElement(g,i({},v.getAttrs(e),{titleAlign:e.attributes.titleAlign,data:[{value:"left",label:"Left"},{value:"center",label:"Center"}]}))),wp.element.createElement(j,{className:"text "+e.attributes.titleAlign,placeholder:"Insert section title",value:e.attributes.title,onChange:function(t){return e.setAttributes({title:t})}})),wp.element.createElement("div",{className:"block-group excerpt"},wp.element.createElement("div",{className:"extend-pickers-group"},wp.element.createElement("div",{className:"group-title"},"Excerpt"),wp.element.createElement(p,i({},b.getAttrs(e),{indicatorText:"Color: "})),wp.element.createElement(w,i({},C.getAttrs(e),{excerptAlign:e.attributes.excerptAlign,data:[{value:"left",label:"Left"},{value:"center",label:"Center"}]}))),wp.element.createElement("div",{style:{color:e.attributes.excerptColor}},wp.element.createElement(j,{className:"text "+e.attributes.excerptAlign,placeholder:"Insert excerpt text",value:e.attributes.excerpt,onChange:function(t){return e.setAttributes({excerpt:t})}}))),wp.element.createElement("div",{className:"block-group thumb"},wp.element.createElement("div",{className:"extend-pickers-group"},wp.element.createElement("div",{className:"group-title"},"ThumbImage"),wp.element.createElement(h,i({},y.getAttrs(e),{imageSize:e.attributes.imageSize,data:[{value:"standard",label:"Standard"},{value:"big",label:"Big"},{value:"small",label:"Small"},{value:"micro",label:"Micro"}]}))),wp.element.createElement("div",{className:e.attributes.imageSize},wp.element.createElement(l.b,l.a.getAttrs(e)))),wp.element.createElement("div",{className:"block-group content"},wp.element.createElement("div",{className:"extend-pickers-group"},wp.element.createElement(E,i({},x.getAttrs(e),{contentAlign:e.attributes.contentAlign,data:[{value:"left",label:"Left"},{value:"center",label:"Center"}]}))),wp.element.createElement(j,{className:"text",placeholder:"Insert content text",value:e.attributes.content,onChange:function(t){return e.setAttributes({content:t})}})))},save:function(){return wp.element.createElement("div",null)}})},function(e,t){},function(e,t,n){"use strict";function r(e,t,n){return t in e?Object.defineProperty(e,t,{value:n,enumerable:!0,configurable:!0,writable:!0}):e[t]=n,e}n.d(t,"a",function(){return i}),n.d(t,"b",function(){return l});var a=n(4),l=(n.n(a),{attrs:{backgroundColor:{type:"string"}},getAttrs:function(){var e=arguments.length>0&&void 0!==arguments[0]?arguments[0]:{},t=e.setAttributes,n=e.attributes;return n=void 0===n?{}:n,{backgroundColor:n.backgroundColor,setAttributes:t}}}),o=function(e){var t=e.backgroundColor,n=void 0===t?"#fff":t,r=e.setAttributes,l=void 0===r?function(){return null}:r,o=e.indicatorText,i=void 0===o?"BG: ":o;return wp.element.createElement("div",null,wp.element.createElement(a.Dropdown,{renderToggle:function(e){var t=e.isOpen,r=e.onToggle;return wp.element.createElement(a.Tooltip,{text:i},wp.element.createElement("button",{type:"button","aria-expanded":t,className:"color-picker-pin",onClick:r},wp.element.createElement("span",{class:"innerText"},i),wp.element.createElement("span",{class:"color-marker",style:{backgroundColor:n}},n)))},renderContent:function(){return wp.element.createElement(a.ColorPicker,{color:n,onChangeComplete:function(e){var t=e.hex;return l({backgroundColor:t})},disableAlpha:!0})}}))},i=function(e){var t=arguments.length>1&&void 0!==arguments[1]?arguments[1]:"";return{config:{attrs:r({},e,{type:"string"}),getAttrs:function(){var n,a=arguments.length>0&&void 0!==arguments[0]?arguments[0]:{},l=a.setAttributes,o=a.attributes,i=void 0===o?{}:o;return n={},r(n,e,i[e]||t),r(n,"setAttributes",l),n}},Comp:function(t){return wp.element.createElement(o,{backgroundColor:t[e],indicatorText:t.indicatorText,setAttributes:function(n){var a=n.backgroundColor;return t.setAttributes(r({},e,a))}})}}};t.c=o},function(e,t){e.exports=wp.components},function(e,t,n){"use strict";n.d(t,"a",function(){return o});var r=wp.editor,a=r.MediaUpload,l=r.MediaUploadCheck,o={attrs:{thumbImage:{type:"object"}},getAttrs:function(){var e=arguments.length>0&&void 0!==arguments[0]?arguments[0]:{},t=e.setAttributes,n=e.attributes;return n=void 0===n?{}:n,{thumbImage:n.thumbImage,setAttributes:t}}},i=function(e){var t=e.thumbImage,n=e.setAttributes;return wp.element.createElement("div",null,t&&wp.element.createElement("img",{src:t.url}),wp.element.createElement(l,null,wp.element.createElement(a,{allowedTypes:["image"],onSelect:function(e){return n({thumbImage:e})},render:function(e){var n=e.open;return wp.element.createElement("button",{className:"default-button",onClick:n},t?"Change image":"Choose image")}})))};t.b=i},function(e,t,n){"use strict";function r(e,t,n){return t in e?Object.defineProperty(e,t,{value:n,enumerable:!0,configurable:!0,writable:!0}):e[t]=n,e}n.d(t,"a",function(){return o}),n.d(t,"b",function(){return a});var a={attrs:{gridCss:{type:"string"},data:{type:{}}},getAttrs:function(){var e=arguments.length>0&&void 0!==arguments[0]?arguments[0]:{},t=e.setAttributes,n=e.attributes;return n=void 0===n?{}:n,{gridCss:n.gridCss,data:n.data,setAttributes:t}}},l=function(e){var t=e.gridCss,n=void 0===t?"default":t,r=e.data,a=void 0===r?{}:r,l=e.setAttributes,o=void 0===l?function(){return null}:l;return wp.element.createElement("div",null,wp.element.createElement("select",{value:n,onChange:function(e){var t=e.target;return o({gridCss:t.value})}},a.map(function(e){return wp.element.createElement("option",{value:e.value},e.label)})))},o=function(e){return{config:{attrs:r({},e,{type:"string"}),getAttrs:function(){var t,n=arguments.length>0&&void 0!==arguments[0]?arguments[0]:{},a=n.setAttributes,l=n.attributes,o=void 0===l?{}:l;return t={},r(t,e,o.key),r(t,"setAttributes",a),t}},Comp:function(t){return wp.element.createElement(l,{gridCss:t[e],data:t.data,setAttributes:function(n){var a=n.gridCss;return t.setAttributes(r({},e,a))}})}}};t.c=l}]);