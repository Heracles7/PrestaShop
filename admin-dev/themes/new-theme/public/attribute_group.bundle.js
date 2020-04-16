window.attribute_group=function(t){function e(o){if(n[o])return n[o].exports;var i=n[o]={i:o,l:!1,exports:{}};return t[o].call(i.exports,i,i.exports,e),i.l=!0,i.exports}var n={};return e.m=t,e.c=n,e.i=function(t){return t},e.d=function(t,n,o){e.o(t,n)||Object.defineProperty(t,n,{configurable:!1,enumerable:!0,get:o})},e.n=function(t){var n=t&&t.__esModule?function(){return t.default}:function(){return t};return e.d(n,"a",n),n},e.o=function(t,e){return Object.prototype.hasOwnProperty.call(t,e)},e.p="",e(e.s=471)}({0:function(t,e,n){"use strict";e.__esModule=!0,e.default=function(t,e){if(!(t instanceof e))throw new TypeError("Cannot call a class as a function")}},1:function(t,e,n){"use strict";e.__esModule=!0;var o=n(19),i=function(t){return t&&t.__esModule?t:{default:t}}(o);e.default=function(){function t(t,e){for(var n=0;n<e.length;n++){var o=e[n];o.enumerable=o.enumerable||!1,o.configurable=!0,"value"in o&&(o.writable=!0),(0,i.default)(t,o.key,o)}}return function(e,n,o){return n&&t(e.prototype,n),o&&t(e,o),e}}()},10:function(t,e,n){var o=n(6),i=n(12);t.exports=n(2)?function(t,e,n){return o.f(t,e,i(1,n))}:function(t,e,n){return t[e]=n,t}},11:function(t,e,n){var o=n(4);t.exports=function(t){if(!o(t))throw TypeError(t+" is not an object!");return t}},12:function(t,e){t.exports=function(t,e){return{enumerable:!(1&t),configurable:!(2&t),writable:!(4&t),value:e}}},13:function(t,e,n){var o=n(4);t.exports=function(t,e){if(!o(t))return t;var n,i;if(e&&"function"==typeof(n=t.toString)&&!o(i=n.call(t)))return i;if("function"==typeof(n=t.valueOf)&&!o(i=n.call(t)))return i;if(!e&&"function"==typeof(n=t.toString)&&!o(i=n.call(t)))return i;throw TypeError("Can't convert object to primitive value")}},141:function(t,e,n){"use strict";function o(t){return t&&t.__esModule?t:{default:t}}Object.defineProperty(e,"__esModule",{value:!0});var i=n(0),r=o(i),a=n(1),l=o(a),s=n(155),u=(o(s),window.$),d=function(){function t(){var e=this;return(0,r.default)(this,t),{extend:function(t){return e.extend(t)}}}return(0,l.default)(t,[{key:"extend",value:function(t){var e=this;this.grid=t,this._addIdsToGridTableRows(),t.getContainer().find(".js-grid-table").tableDnD({onDragClass:"position-row-while-drag",dragHandle:".js-drag-handle",onDrop:function(t,n){return e._handlePositionChange(n)}}),t.getContainer().find(".js-drag-handle").hover(function(){u(this).closest("tr").addClass("hover")},function(){u(this).closest("tr").removeClass("hover")})}},{key:"_handlePositionChange",value:function(t){var e=u(t).find(".js-"+this.grid.getId()+"-position:first"),n=e.data("update-url"),o=e.data("update-method"),i=parseInt(e.data("pagination-offset"),10),r=this._getRowsPositions(i),a={positions:r};this._updatePosition(n,a,o)}},{key:"_getRowsPositions",value:function(t){var e=JSON.parse(u.tableDnD.jsonize()),n=e[this.grid.getId()+"_grid_table"],o=/^row_(\d+)_(\d+)$/,i=n.length,r=[],a=void 0,l=void 0;for(l=0;l<i;++l)a=o.exec(n[l]),r.push({rowId:a[1],newPosition:t+l,oldPosition:parseInt(a[2],10)});return r}},{key:"_addIdsToGridTableRows",value:function(){this.grid.getContainer().find(".js-grid-table .js-"+this.grid.getId()+"-position").each(function(t,e){var n=u(e),o=n.data("id"),i=n.data("position"),r="row_"+o+"_"+i;n.closest("tr").attr("id",r),n.closest("td").addClass("js-drag-handle")})}},{key:"_updatePosition",value:function(t,e,n){for(var o=["GET","POST"].includes(n),i=u("<form>",{action:t,method:o?n:"POST"}).appendTo("body"),r=e.positions.length,a=void 0,l=0;l<r;++l)a=e.positions[l],i.append(u("<input>",{type:"hidden",name:"positions["+l+"][rowId]",value:a.rowId}),u("<input>",{type:"hidden",name:"positions["+l+"][oldPosition]",value:a.oldPosition}),u("<input>",{type:"hidden",name:"positions["+l+"][newPosition]",value:a.newPosition}));o||i.append(u("<input>",{type:"hidden",name:"_method",value:n})),i.submit()}}]),t}();e.default=d},15:function(t,e,n){var o=n(18);t.exports=function(t,e,n){if(o(t),void 0===e)return t;switch(n){case 1:return function(n){return t.call(e,n)};case 2:return function(n,o){return t.call(e,n,o)};case 3:return function(n,o,i){return t.call(e,n,o,i)}}return function(){return t.apply(e,arguments)}}},155:function(t,e,n){(function(t){/*! jquery.tablednd.js 30-12-2017 */
!function(e,n,o,i){var r="touchstart mousedown",a="touchmove mousemove",l="touchend mouseup";e(o).ready(function(){function t(t){for(var e={},n=t.match(/([^;:]+)/g)||[];n.length;)e[n.shift()]=n.shift().trim();return e}e("table").each(function(){"dnd"===e(this).data("table")&&e(this).tableDnD({onDragStyle:e(this).data("ondragstyle")&&t(e(this).data("ondragstyle"))||null,onDropStyle:e(this).data("ondropstyle")&&t(e(this).data("ondropstyle"))||null,onDragClass:void 0===e(this).data("ondragclass")&&"tDnD_whileDrag"||e(this).data("ondragclass"),onDrop:e(this).data("ondrop")&&new Function("table","row",e(this).data("ondrop")),onDragStart:e(this).data("ondragstart")&&new Function("table","row",e(this).data("ondragstart")),onDragStop:e(this).data("ondragstop")&&new Function("table","row",e(this).data("ondragstop")),scrollAmount:e(this).data("scrollamount")||5,sensitivity:e(this).data("sensitivity")||10,hierarchyLevel:e(this).data("hierarchylevel")||0,indentArtifact:e(this).data("indentartifact")||'<div class="indent">&nbsp;</div>',autoWidthAdjust:e(this).data("autowidthadjust")||!0,autoCleanRelations:e(this).data("autocleanrelations")||!0,jsonPretifySeparator:e(this).data("jsonpretifyseparator")||"\t",serializeRegexp:e(this).data("serializeregexp")&&new RegExp(e(this).data("serializeregexp"))||/[^\-]*$/,serializeParamName:e(this).data("serializeparamname")||!1,dragHandle:e(this).data("draghandle")||null})})}),t.tableDnD={currentTable:null,dragObject:null,mouseOffset:null,oldX:0,oldY:0,build:function(t){return this.each(function(){this.tableDnDConfig=e.extend({onDragStyle:null,onDropStyle:null,onDragClass:"tDnD_whileDrag",onDrop:null,onDragStart:null,onDragStop:null,scrollAmount:5,sensitivity:10,hierarchyLevel:0,indentArtifact:'<div class="indent">&nbsp;</div>',autoWidthAdjust:!0,autoCleanRelations:!0,jsonPretifySeparator:"\t",serializeRegexp:/[^\-]*$/,serializeParamName:!1,dragHandle:null},t||{}),e.tableDnD.makeDraggable(this),this.tableDnDConfig.hierarchyLevel&&e.tableDnD.makeIndented(this)}),this},makeIndented:function(t){var n,o,i=t.tableDnDConfig,r=t.rows,a=e(r).first().find("td:first")[0],l=0,s=0;if(e(t).hasClass("indtd"))return null;o=e(t).addClass("indtd").attr("style"),e(t).css({whiteSpace:"nowrap"});for(var u=0;u<r.length;u++)s<e(r[u]).find("td:first").text().length&&(s=e(r[u]).find("td:first").text().length,n=u);for(e(a).css({width:"auto"}),u=0;u<i.hierarchyLevel;u++)e(r[n]).find("td:first").prepend(i.indentArtifact);for(a&&e(a).css({width:a.offsetWidth}),o&&e(t).css(o),u=0;u<i.hierarchyLevel;u++)e(r[n]).find("td:first").children(":first").remove();return i.hierarchyLevel&&e(r).each(function(){(l=e(this).data("level")||0)<=i.hierarchyLevel&&e(this).data("level",l)||e(this).data("level",0);for(var t=0;t<e(this).data("level");t++)e(this).find("td:first").prepend(i.indentArtifact)}),this},makeDraggable:function(t){var n=t.tableDnDConfig;n.dragHandle&&e(n.dragHandle,t).each(function(){e(this).bind(r,function(o){return e.tableDnD.initialiseDrag(e(this).parents("tr")[0],t,this,o,n),!1})})||e(t.rows).each(function(){e(this).hasClass("nodrag")?e(this).css("cursor",""):e(this).bind(r,function(o){if("TD"===o.target.tagName)return e.tableDnD.initialiseDrag(this,t,this,o,n),!1}).css("cursor","move")})},currentOrder:function(){var t=this.currentTable.rows;return e.map(t,function(t){return(e(t).data("level")+t.id).replace(/\s/g,"")}).join("")},initialiseDrag:function(t,n,i,r,s){this.dragObject=t,this.currentTable=n,this.mouseOffset=this.getMouseOffset(i,r),this.originalOrder=this.currentOrder(),e(o).bind(a,this.mousemove).bind(l,this.mouseup),s.onDragStart&&s.onDragStart(n,i)},updateTables:function(){this.each(function(){this.tableDnDConfig&&e.tableDnD.makeDraggable(this)})},mouseCoords:function(t){return t.originalEvent.changedTouches?{x:t.originalEvent.changedTouches[0].clientX,y:t.originalEvent.changedTouches[0].clientY}:t.pageX||t.pageY?{x:t.pageX,y:t.pageY}:{x:t.clientX+o.body.scrollLeft-o.body.clientLeft,y:t.clientY+o.body.scrollTop-o.body.clientTop}},getMouseOffset:function(t,e){var o,i;return e=e||n.event,i=this.getPosition(t),o=this.mouseCoords(e),{x:o.x-i.x,y:o.y-i.y}},getPosition:function(t){var e=0,n=0;for(0===t.offsetHeight&&(t=t.firstChild);t.offsetParent;)e+=t.offsetLeft,n+=t.offsetTop,t=t.offsetParent;return e+=t.offsetLeft,n+=t.offsetTop,{x:e,y:n}},autoScroll:function(t){var e=this.currentTable.tableDnDConfig,i=n.pageYOffset,r=n.innerHeight?n.innerHeight:o.documentElement.clientHeight?o.documentElement.clientHeight:o.body.clientHeight;o.all&&(void 0!==o.compatMode&&"BackCompat"!==o.compatMode?i=o.documentElement.scrollTop:void 0!==o.body&&(i=o.body.scrollTop)),t.y-i<e.scrollAmount&&n.scrollBy(0,-e.scrollAmount)||r-(t.y-i)<e.scrollAmount&&n.scrollBy(0,e.scrollAmount)},moveVerticle:function(t,e){0!==t.vertical&&e&&this.dragObject!==e&&this.dragObject.parentNode===e.parentNode&&(0>t.vertical&&this.dragObject.parentNode.insertBefore(this.dragObject,e.nextSibling)||0<t.vertical&&this.dragObject.parentNode.insertBefore(this.dragObject,e))},moveHorizontal:function(t,n){var o,i=this.currentTable.tableDnDConfig;if(!i.hierarchyLevel||0===t.horizontal||!n||this.dragObject!==n)return null;o=e(n).data("level"),0<t.horizontal&&o>0&&e(n).find("td:first").children(":first").remove()&&e(n).data("level",--o),0>t.horizontal&&o<i.hierarchyLevel&&e(n).prev().data("level")>=o&&e(n).children(":first").prepend(i.indentArtifact)&&e(n).data("level",++o)},mousemove:function(t){var n,o,i,r,a,l=e(e.tableDnD.dragObject),s=e.tableDnD.currentTable.tableDnDConfig;return t&&t.preventDefault(),!!e.tableDnD.dragObject&&("touchmove"===t.type&&event.preventDefault(),s.onDragClass&&l.addClass(s.onDragClass)||l.css(s.onDragStyle),o=e.tableDnD.mouseCoords(t),r=o.x-e.tableDnD.mouseOffset.x,a=o.y-e.tableDnD.mouseOffset.y,e.tableDnD.autoScroll(o),n=e.tableDnD.findDropTargetRow(l,a),i=e.tableDnD.findDragDirection(r,a),e.tableDnD.moveVerticle(i,n),e.tableDnD.moveHorizontal(i,n),!1)},findDragDirection:function(t,e){var n=this.currentTable.tableDnDConfig.sensitivity,o=this.oldX,i=this.oldY,r=o-n,a=o+n,l=i-n,s=i+n,u={horizontal:t>=r&&t<=a?0:t>o?-1:1,vertical:e>=l&&e<=s?0:e>i?-1:1};return 0!==u.horizontal&&(this.oldX=t),0!==u.vertical&&(this.oldY=e),u},findDropTargetRow:function(t,n){for(var o=0,i=this.currentTable.rows,r=this.currentTable.tableDnDConfig,a=0,l=null,s=0;s<i.length;s++)if(l=i[s],a=this.getPosition(l).y,o=parseInt(l.offsetHeight)/2,0===l.offsetHeight&&(a=this.getPosition(l.firstChild).y,o=parseInt(l.firstChild.offsetHeight)/2),n>a-o&&n<a+o)return t.is(l)||r.onAllowDrop&&!r.onAllowDrop(t,l)||e(l).hasClass("nodrop")?null:l;return null},processMouseup:function(){if(!this.currentTable||!this.dragObject)return null;var t=this.currentTable.tableDnDConfig,n=this.dragObject,i=0,r=0;e(o).unbind(a,this.mousemove).unbind(l,this.mouseup),t.hierarchyLevel&&t.autoCleanRelations&&e(this.currentTable.rows).first().find("td:first").children().each(function(){(r=e(this).parents("tr:first").data("level"))&&e(this).parents("tr:first").data("level",--r)&&e(this).remove()})&&t.hierarchyLevel>1&&e(this.currentTable.rows).each(function(){if((r=e(this).data("level"))>1)for(i=e(this).prev().data("level");r>i+1;)e(this).find("td:first").children(":first").remove(),e(this).data("level",--r)}),t.onDragClass&&e(n).removeClass(t.onDragClass)||e(n).css(t.onDropStyle),this.dragObject=null,t.onDrop&&this.originalOrder!==this.currentOrder()&&e(n).hide().fadeIn("fast")&&t.onDrop(this.currentTable,n),t.onDragStop&&t.onDragStop(this.currentTable,n),this.currentTable=null},mouseup:function(t){return t&&t.preventDefault(),e.tableDnD.processMouseup(),!1},jsonize:function(t){var e=this.currentTable;return t?JSON.stringify(this.tableData(e),null,e.tableDnDConfig.jsonPretifySeparator):JSON.stringify(this.tableData(e))},serialize:function(){return e.param(this.tableData(this.currentTable))},serializeTable:function(t){for(var e="",n=t.tableDnDConfig.serializeParamName||t.id,o=t.rows,i=0;i<o.length;i++){e.length>0&&(e+="&");var r=o[i].id;r&&t.tableDnDConfig&&t.tableDnDConfig.serializeRegexp&&(r=r.match(t.tableDnDConfig.serializeRegexp)[0],e+=n+"[]="+r)}return e},serializeTables:function(){var t=[];return e("table").each(function(){this.id&&t.push(e.param(e.tableDnD.tableData(this)))}),t.join("&")},tableData:function(t){var n,o,i,r,a=t.tableDnDConfig,l=[],s=0,u=0,d=null,c={};if(t||(t=this.currentTable),!t||!t.rows||!t.rows.length)return{error:{code:500,message:"Not a valid table."}};if(!t.id&&!a.serializeParamName)return{error:{code:500,message:"No serializable unique id provided."}};r=a.autoCleanRelations&&t.rows||e.makeArray(t.rows),o=a.serializeParamName||t.id,i=o,n=function(t){return t&&a&&a.serializeRegexp?t.match(a.serializeRegexp)[0]:t},c[i]=[],!a.autoCleanRelations&&e(r[0]).data("level")&&r.unshift({id:"undefined"});for(var f=0;f<r.length;f++)if(a.hierarchyLevel){if(0===(u=e(r[f]).data("level")||0))i=o,l=[];else if(u>s)l.push([i,s]),i=n(r[f-1].id);else if(u<s)for(var h=0;h<l.length;h++)l[h][1]===u&&(i=l[h][0]),l[h][1]>=s&&(l[h][1]=0);s=u,e.isArray(c[i])||(c[i]=[]),(d=n(r[f].id))&&c[i].push(d)}else(d=n(r[f].id))&&c[i].push(d);return c}},t.fn.extend({tableDnD:e.tableDnD.build,tableDnDUpdate:e.tableDnD.updateTables,tableDnDSerialize:e.proxy(e.tableDnD.serialize,e.tableDnD),tableDnDSerializeAll:e.tableDnD.serializeTables,tableDnDData:e.proxy(e.tableDnD.tableData,e.tableDnD)})}(t,window,window.document)}).call(e,n(41))},16:function(t,e,n){var o=n(4),i=n(5).document,r=o(i)&&o(i.createElement);t.exports=function(t){return r?i.createElement(t):{}}},17:function(t,e,n){t.exports=!n(2)&&!n(7)(function(){return 7!=Object.defineProperty(n(16)("div"),"a",{get:function(){return 7}}).a})},18:function(t,e){t.exports=function(t){if("function"!=typeof t)throw TypeError(t+" is not a function!");return t}},19:function(t,e,n){t.exports={default:n(20),__esModule:!0}},2:function(t,e,n){t.exports=!n(7)(function(){return 7!=Object.defineProperty({},"a",{get:function(){return 7}}).a})},20:function(t,e,n){n(21);var o=n(3).Object;t.exports=function(t,e,n){return o.defineProperty(t,e,n)}},21:function(t,e,n){var o=n(8);o(o.S+o.F*!n(2),"Object",{defineProperty:n(6).f})},23:function(t,e,n){"use strict";function o(t){return t&&t.__esModule?t:{default:t}}Object.defineProperty(e,"__esModule",{value:!0});var i=n(0),r=o(i),a=n(1),l=o(a),s=window.$,u=function(){function t(e){(0,r.default)(this,t),this.id=e,this.$container=s("#"+this.id+"_grid")}return(0,l.default)(t,[{key:"getId",value:function(){return this.id}},{key:"getContainer",value:function(){return this.$container}},{key:"getHeaderContainer",value:function(){return this.$container.closest(".js-grid-panel").find(".js-grid-header")}},{key:"addExtension",value:function(t){t.extend(this)}}]),t}();e.default=u},24:function(t,e,n){"use strict";function o(t){return t&&t.__esModule?t:{default:t}}Object.defineProperty(e,"__esModule",{value:!0});var i=n(0),r=o(i),a=n(1),l=o(a),s=n(42),u=o(s),d=window.$,c=function(){function t(){(0,r.default)(this,t)}return(0,l.default)(t,[{key:"extend",value:function(t){t.getContainer().on("click",".js-reset-search",function(t){(0,u.default)(d(t.currentTarget).data("url"),d(t.currentTarget).data("redirect"))})}}]),t}();e.default=c},25:function(t,e,n){"use strict";function o(t){return t&&t.__esModule?t:{default:t}}Object.defineProperty(e,"__esModule",{value:!0});var i=n(0),r=o(i),a=n(1),l=o(a),s=n(40),u=o(s),d=function(){function t(){(0,r.default)(this,t)}return(0,l.default)(t,[{key:"extend",value:function(t){var e=t.getContainer().find("table.table");new u.default(e).attach()}}]),t}();/**
      * 2007-2020 PrestaShop SA and Contributors
      *
      * NOTICE OF LICENSE
      *
      * This source file is subject to the Open Software License (OSL 3.0)
      * that is bundled with this package in the file LICENSE.txt.
      * It is also available through the world-wide-web at this URL:
      * https://opensource.org/licenses/OSL-3.0
      * If you did not receive a copy of the license and are unable to
      * obtain it through the world-wide-web, please send an email
      * to license@prestashop.com so we can send you a copy immediately.
      *
      * DISCLAIMER
      *
      * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
      * versions in the future. If you wish to customize PrestaShop for your
      * needs please refer to https://www.prestashop.com for more information.
      *
      * @author    PrestaShop SA <contact@prestashop.com>
      * @copyright 2007-2020 PrestaShop SA and Contributors
      * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
      * International Registered Trademark & Property of PrestaShop SA
      */
e.default=d},26:function(t,e,n){"use strict";function o(t){return t&&t.__esModule?t:{default:t}}Object.defineProperty(e,"__esModule",{value:!0});var i=n(0),r=o(i),a=n(1),l=o(a),s=function(){function t(){(0,r.default)(this,t)}return(0,l.default)(t,[{key:"extend",value:function(t){t.getHeaderContainer().on("click",".js-common_refresh_list-grid-action",function(){location.reload()})}}]),t}();e.default=s},28:function(t,e,n){"use strict";function o(t){return t&&t.__esModule?t:{default:t}}Object.defineProperty(e,"__esModule",{value:!0});var i=n(0),r=o(i),a=n(1),l=o(a),s=window.$,u=function(){function t(){(0,r.default)(this,t)}return(0,l.default)(t,[{key:"extend",value:function(t){var e=this;t.getHeaderContainer().on("click",".js-common_show_query-grid-action",function(){return e._onShowSqlQueryClick(t)}),t.getHeaderContainer().on("click",".js-common_export_sql_manager-grid-action",function(){return e._onExportSqlManagerClick(t)})}},{key:"_onShowSqlQueryClick",value:function(t){var e=s("#"+t.getId()+"_common_show_query_modal_form");this._fillExportForm(e,t);var n=s("#"+t.getId()+"_grid_common_show_query_modal");n.modal("show"),n.on("click",".btn-sql-submit",function(){return e.submit()})}},{key:"_onExportSqlManagerClick",value:function(t){var e=s("#"+t.getId()+"_common_show_query_modal_form");this._fillExportForm(e,t),e.submit()}},{key:"_fillExportForm",value:function(t,e){var n=e.getContainer().find(".js-grid-table").data("query");t.find('textarea[name="sql"]').val(n),t.find('input[name="name"]').val(this._getNameFromBreadcrumb())}},{key:"_getNameFromBreadcrumb",value:function(){var t=s(".header-toolbar").find(".breadcrumb-item"),e="";return t.each(function(t,n){var o=s(n),i=0<o.find("a").length?o.find("a").text():o.text();0<e.length&&(e=e.concat(" > ")),e=e.concat(i)}),e}}]),t}();e.default=u},3:function(t,e){var n=t.exports={version:"2.4.0"};"number"==typeof __e&&(__e=n)},30:function(t,e,n){"use strict";function o(t){return t&&t.__esModule?t:{default:t}}Object.defineProperty(e,"__esModule",{value:!0});var i=n(0),r=o(i),a=n(1),l=o(a),s=function(){function t(){(0,r.default)(this,t)}return(0,l.default)(t,[{key:"extend",value:function(t){var e=t.getContainer().find(".column-filters");e.find(".grid-search-button").prop("disabled",!0),e.find("input:not(.js-bulk-action-select-all), select").on("input dp.change",function(){e.find(".grid-search-button").prop("disabled",!1),e.find(".js-grid-reset-button").prop("hidden",!1)})}}]),t}();e.default=s},31:function(t,e,n){"use strict";function o(t){return t&&t.__esModule?t:{default:t}}Object.defineProperty(e,"__esModule",{value:!0});var i=n(0),r=o(i),a=n(1),l=o(a),s=window.$,u=function(){function t(){(0,r.default)(this,t)}return(0,l.default)(t,[{key:"extend",value:function(t){this._handleBulkActionCheckboxSelect(t),this._handleBulkActionSelectAllCheckbox(t)}},{key:"_handleBulkActionSelectAllCheckbox",value:function(t){var e=this;t.getContainer().on("change",".js-bulk-action-select-all",function(n){var o=s(n.currentTarget),i=o.is(":checked");i?e._enableBulkActionsBtn(t):e._disableBulkActionsBtn(t),t.getContainer().find(".js-bulk-action-checkbox").prop("checked",i)})}},{key:"_handleBulkActionCheckboxSelect",value:function(t){var e=this;t.getContainer().on("change",".js-bulk-action-checkbox",function(){t.getContainer().find(".js-bulk-action-checkbox:checked").length>0?e._enableBulkActionsBtn(t):e._disableBulkActionsBtn(t)})}},{key:"_enableBulkActionsBtn",value:function(t){t.getContainer().find(".js-bulk-actions-btn").prop("disabled",!1)}},{key:"_disableBulkActionsBtn",value:function(t){t.getContainer().find(".js-bulk-actions-btn").prop("disabled",!0)}}]),t}();e.default=u},32:function(t,e,n){"use strict";function o(t){return t&&t.__esModule?t:{default:t}}Object.defineProperty(e,"__esModule",{value:!0});var i=n(0),r=o(i),a=n(1),l=o(a),s=n(46),u=o(s),d=window.$,c=function(){function t(){var e=this;return(0,r.default)(this,t),{extend:function(t){return e.extend(t)}}}return(0,l.default)(t,[{key:"extend",value:function(t){var e=this;t.getContainer().on("click",".js-bulk-action-submit-btn",function(n){e.submit(n,t)})}},{key:"submit",value:function(t,e){var n=d(t.currentTarget),o=n.data("confirm-message"),i=n.data("confirmTitle");void 0!==o&&0<o.length?void 0!==i?this.showConfirmModal(n,e,o,i):confirm(o)&&this.postForm(n,e):this.postForm(n,e)}},{key:"showConfirmModal",value:function(t,e,n,o){var i=this,r=t.data("confirmButtonLabel"),a=t.data("closeButtonLabel"),l=t.data("confirmButtonClass");new u.default({id:e.getId()+"_grid_confirm_modal",confirmTitle:o,confirmMessage:n,confirmButtonLabel:r,closeButtonLabel:a,confirmButtonClass:l},function(){return i.postForm(t,e)}).show()}},{key:"postForm",value:function(t,e){var n=d("#"+e.getId()+"_filter_form");n.attr("action",t.data("form-url")),n.attr("method",t.data("form-method")),n.submit()}}]),t}();e.default=c},35:function(t,e,n){"use strict";function o(t){return t&&t.__esModule?t:{default:t}}Object.defineProperty(e,"__esModule",{value:!0});var i=n(0),r=o(i),a=n(1),l=o(a),s=window.$,u=function(){function t(){(0,r.default)(this,t)}return(0,l.default)(t,[{key:"extend",value:function(t){t.getContainer().on("click",".js-submit-row-action",function(t){t.preventDefault();var e=s(t.currentTarget),n=e.data("confirm-message");if(!n.length||confirm(n)){var o=e.data("method"),i=["GET","POST"].includes(o),r=s("<form>",{action:e.data("url"),method:i?o:"POST"}).appendTo("body");i||r.append(s("<input>",{type:"_hidden",name:"_method",value:o})),r.submit()}})}}]),t}();e.default=u},4:function(t,e){t.exports=function(t){return"object"==typeof t?null!==t:"function"==typeof t}},40:function(t,e,n){"use strict";(function(t){function o(t){return t&&t.__esModule?t:{default:t}}Object.defineProperty(e,"__esModule",{value:!0});var i=n(0),r=o(i),a=n(1),l=o(a),s=t.$,u=function(){function t(e){(0,r.default)(this,t),this.selector=".ps-sortable-column",this.columns=s(e).find(this.selector)}return(0,l.default)(t,[{key:"attach",value:function(){var t=this;this.columns.on("click",function(e){var n=s(e.delegateTarget);t._sortByColumn(n,t._getToggledSortDirection(n))})}},{key:"sortBy",value:function(t,e){var n=this.columns.is('[data-sort-col-name="'+t+'"]');if(!n)throw new Error('Cannot sort by "'+t+'": invalid column');this._sortByColumn(n,e)}},{key:"_sortByColumn",value:function(t,e){window.location=this._getUrl(t.data("sortColName"),"desc"===e?"desc":"asc",t.data("sortPrefix"))}},{key:"_getToggledSortDirection",value:function(t){return"asc"===t.data("sortDirection")?"desc":"asc"}},{key:"_getUrl",value:function(t,e,n){var o=new URL(window.location.href),i=o.searchParams;return n?(i.set(n+"[orderBy]",t),i.set(n+"[sortOrder]",e)):(i.set("orderBy",t),i.set("sortOrder",e)),o.toString()}}]),t}();e.default=u}).call(e,n(9))},41:function(t,e){!function(){t.exports=window.jQuery}()},42:function(t,e,n){"use strict";(function(t){Object.defineProperty(e,"__esModule",{value:!0});/**
 * 2007-2020 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
var n=t.$,o=function(t,e){n.post(t).then(function(){return window.location.assign(e)})};e.default=o}).call(e,n(9))},46:function(t,e,n){"use strict";function o(t,e){var n=this,o=t.id,a=t.closable;this.modal=i(t),this.$modal=r(this.modal.container),this.show=function(){n.$modal.modal()},this.modal.confirmButton.addEventListener("click",e),this.$modal.modal({backdrop:!!a||"static",keyboard:void 0===a||a,closable:void 0===a||a,show:!1}),this.$modal.on("hidden.bs.modal",function(){document.querySelector("#"+o).remove()}),document.body.appendChild(this.modal.container)}function i(t){var e=t.id,n=void 0===e?"confirm_modal":e,o=t.confirmTitle,i=t.confirmMessage,r=void 0===i?"":i,a=t.closeButtonLabel,l=void 0===a?"Close":a,s=t.confirmButtonLabel,u=void 0===s?"Accept":s,d=t.confirmButtonClass,c=void 0===d?"btn-primary":d,f={};return f.container=document.createElement("div"),f.container.classList.add("modal","fade"),f.container.id=n,f.dialog=document.createElement("div"),f.dialog.classList.add("modal-dialog"),f.content=document.createElement("div"),f.content.classList.add("modal-content"),f.header=document.createElement("div"),f.header.classList.add("modal-header"),o&&(f.title=document.createElement("h4"),f.title.classList.add("modal-title"),f.title.innerHTML=o),f.closeIcon=document.createElement("button"),f.closeIcon.classList.add("close"),f.closeIcon.setAttribute("type","button"),f.closeIcon.dataset.dismiss="modal",f.closeIcon.innerHTML="×",f.body=document.createElement("div"),f.body.classList.add("modal-body","text-left","font-weight-normal"),f.message=document.createElement("p"),f.message.classList.add("confirm-message"),f.message.innerHTML=r,f.footer=document.createElement("div"),f.footer.classList.add("modal-footer"),f.closeButton=document.createElement("button"),f.closeButton.setAttribute("type","button"),f.closeButton.classList.add("btn","btn-outline-secondary","btn-lg"),f.closeButton.dataset.dismiss="modal",f.closeButton.innerHTML=l,f.confirmButton=document.createElement("button"),f.confirmButton.setAttribute("type","button"),f.confirmButton.classList.add("btn",c,"btn-lg","btn-confirm-submit"),f.confirmButton.dataset.dismiss="modal",f.confirmButton.innerHTML=u,o?f.header.append(f.title,f.closeIcon):f.header.appendChild(f.closeIcon),f.body.appendChild(f.message),f.footer.append(f.closeButton,f.confirmButton),f.content.append(f.header,f.body,f.footer),f.dialog.appendChild(f.content),f.container.appendChild(f.dialog),f}Object.defineProperty(e,"__esModule",{value:!0}),e.default=o;/**
 * 2007-2020 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
var r=window.$},471:function(t,e,n){"use strict";function o(t){return t&&t.__esModule?t:{default:t}}var i=n(23),r=o(i),a=n(25),l=o(a),s=n(24),u=o(s),d=n(26),c=o(d),f=n(35),h=o(f),p=n(32),v=o(p),g=n(31),b=o(g),m=n(28),y=o(m),D=n(30),_=o(D),w=n(78),C=o(w),x=n(77),k=o(x),j=n(141),T=o(j);(0,window.$)(function(){var t=new r.default("attribute_group");t.addExtension(new y.default),t.addExtension(new c.default),t.addExtension(new l.default),t.addExtension(new u.default),t.addExtension(new h.default),t.addExtension(new v.default),t.addExtension(new b.default),t.addExtension(new _.default),t.addExtension(new T.default),new C.default("attributesShowcaseCard").addExtension(new k.default)})},5:function(t,e){var n=t.exports="undefined"!=typeof window&&window.Math==Math?window:"undefined"!=typeof self&&self.Math==Math?self:Function("return this")();"number"==typeof __g&&(__g=n)},6:function(t,e,n){var o=n(11),i=n(17),r=n(13),a=Object.defineProperty;e.f=n(2)?Object.defineProperty:function(t,e,n){if(o(t),e=r(e,!0),o(n),i)try{return a(t,e,n)}catch(t){}if("get"in n||"set"in n)throw TypeError("Accessors not supported!");return"value"in n&&(t[e]=n.value),t}},7:function(t,e){t.exports=function(t){try{return!!t()}catch(t){return!0}}},77:function(t,e,n){"use strict";function o(t){return t&&t.__esModule?t:{default:t}}Object.defineProperty(e,"__esModule",{value:!0});var i=n(0),r=o(i),a=n(1),l=o(a),s=window.$,u=function(){function t(){(0,r.default)(this,t)}return(0,l.default)(t,[{key:"extend",value:function(t){var e=t.getContainer();e.on("click",".js-remove-helper-block",function(t){e.remove();var n=s(t.target),o=n.data("closeUrl"),i=n.data("cardName");o&&s.post(o,{close:1,name:i})})}}]),t}();e.default=u},78:function(t,e,n){"use strict";function o(t){return t&&t.__esModule?t:{default:t}}Object.defineProperty(e,"__esModule",{value:!0});var i=n(0),r=o(i),a=n(1),l=o(a),s=window.$,u=function(){function t(e){(0,r.default)(this,t),this.id=e,this.$container=s("#"+this.id)}return(0,l.default)(t,[{key:"getContainer",value:function(){return this.$container}},{key:"addExtension",value:function(t){t.extend(this)}}]),t}();e.default=u},8:function(t,e,n){var o=n(5),i=n(3),r=n(15),a=n(10),l=function(t,e,n){var s,u,d,c=t&l.F,f=t&l.G,h=t&l.S,p=t&l.P,v=t&l.B,g=t&l.W,b=f?i:i[e]||(i[e]={}),m=b.prototype,y=f?o:h?o[e]:(o[e]||{}).prototype;f&&(n=e);for(s in n)(u=!c&&y&&void 0!==y[s])&&s in b||(d=u?y[s]:n[s],b[s]=f&&"function"!=typeof y[s]?n[s]:v&&u?r(d,o):g&&y[s]==d?function(t){var e=function(e,n,o){if(this instanceof t){switch(arguments.length){case 0:return new t;case 1:return new t(e);case 2:return new t(e,n)}return new t(e,n,o)}return t.apply(this,arguments)};return e.prototype=t.prototype,e}(d):p&&"function"==typeof d?r(Function.call,d):d,p&&((b.virtual||(b.virtual={}))[s]=d,t&l.R&&m&&!m[s]&&a(m,s,d)))};l.F=1,l.G=2,l.S=4,l.P=8,l.B=16,l.W=32,l.U=64,l.R=128,t.exports=l},9:function(t,e){var n;n=function(){return this}();try{n=n||Function("return this")()||(0,eval)("this")}catch(t){"object"==typeof window&&(n=window)}t.exports=n}});