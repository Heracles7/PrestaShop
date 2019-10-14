window.order_create=function(e){function t(n){if(r[n])return r[n].exports;var o=r[n]={i:n,l:!1,exports:{}};return e[n].call(o.exports,o,o.exports,t),o.l=!0,o.exports}var r={};return t.m=e,t.c=r,t.i=function(e){return e},t.d=function(e,r,n){t.o(e,r)||Object.defineProperty(e,r,{configurable:!1,enumerable:!0,get:n})},t.n=function(e){var r=e&&e.__esModule?function(){return e.default}:function(){return e};return t.d(r,"a",r),r},t.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},t.p="",t(t.s=351)}({266:function(e,t,r){"use strict";function n(e){return e&&e.__esModule?e:{default:e}}function o(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}Object.defineProperty(t,"__esModule",{value:!0});var a=function(){function e(e,t){for(var r=0;r<t.length;r++){var n=t[r];n.enumerable=n.enumerable||!1,n.configurable=!0,"value"in n&&(n.writable=!0),Object.defineProperty(e,n.key,n)}}return function(t,r,n){return r&&e(t.prototype,r),n&&e(t,n),t}}(),s=r(33),u=n(s),i=r(356),c=n(i),d=r(358),l=n(d),f=r(353),h=n(f),m=r(355),v=n(m),p=r(354),y=n(p),C=r(357),_=n(C),b=r(352),w=n(b),g=r(359),k=n(g),S=r(70),j=n(S),R=window.$,x=function(){function e(){var t=this;return o(this,e),this.data={},this.$container=R(u.default.orderCreationContainer),this.cartProvider=new h.default,this.customerInfoProvider=new v.default,this.customerSearcher=new c.default,this.shippingRenderer=new l.default,this.cartsRenderer=new y.default,this.ordersRenderer=new _.default,this.addressesRenderer=new w.default,this.vouchersRenderer=new k.default,this.router=new j.default,{listenForCustomerSearch:function(){return t._handleCustomerSearch()},listenForCustomerSelect:function(){return t._handleCustomerChooseForOrderCreation()},listenForCartSelect:function(){return t._handleUseCartForOrderCreation()},listenForOrderSelect:function(){return t._handleDuplicateOrderCart()},listenForCartUpdate:function(){return t._handleCartUpdate()}}}return a(e,[{key:"_handleCustomerSearch",value:function(){var e=this;this.$container.on("input",u.default.customerSearchInput,function(){e.customerSearcher.onCustomerSearch()})}},{key:"_handleCustomerChooseForOrderCreation",value:function(){var e=this;this.$container.on("click",u.default.chooseCustomerBtn,function(t){var r=e.customerSearcher.onCustomerChooseForOrderCreation(t);e.data.customer_id=r,e.cartProvider.loadEmptyCart(r).then(function(t){e.data.cart_id=t.cartId,e._renderCartInfo(t)}),e._loadCustomerCarts(r),e._loadCustomerOrders(r)}),this.$container.on("click",u.default.changeCustomerBtn,function(){return e.customerSearcher.onCustomerChange()})}},{key:"_handleUseCartForOrderCreation",value:function(){var e=this;this.$container.on("click",".js-use-cart-btn",function(t){var r=R(t.currentTarget).data("cart-id");e.cartProvider.getCart(r).then(function(t){e._renderCartInfo(t)})})}},{key:"_handleDuplicateOrderCart",value:function(){var e=this;this.$container.on("click",".js-use-order-btn",function(t){var r=R(t.currentTarget).data("order-id");e.cartProvider.duplicateOrderCart(r).then(function(t){e._renderCartInfo(t)})})}},{key:"_handleCartUpdate",value:function(){var e=this;this.$container.on("change",u.default.addressSelect,function(){return e._changeCartAddresses()})}},{key:"_loadCustomerCarts",value:function(e){var t=this;this.customerInfoProvider.getCustomerCarts(e).then(function(e){t.cartsRenderer.render({carts:e.carts,currentCartId:t.data.cart_id}),R(u.default.customerCheckoutHistory).removeClass("d-none")})}},{key:"_loadCustomerOrders",value:function(e){var t=this;this.customerInfoProvider.getCustomerOrders(e).then(function(e){t.ordersRenderer.render(e.orders),R(u.default.customerCheckoutHistory).removeClass("d-none")})}},{key:"_renderCartInfo",value:function(e){this.addressesRenderer.render(e.addresses),this.vouchersRenderer.render(e.cartRules),this._showCartInfo()}},{key:"_showCartInfo",value:function(){R(u.default.cartBlock).removeClass("d-none"),R(u.default.vouchersBlock).removeClass("d-none"),R(u.default.addressesBlock).removeClass("d-none")}},{key:"_changeCartAddresses",value:function(){var e=this;R.ajax(this.$container.data("edit-address-url"),{method:"POST",data:{cart_id:this.data.cart_id,delivery_address_id:R(u.default.deliveryAddressSelect).val(),invoice_address_id:R(u.default.invoiceAddressSelect).val()},dataType:"json"}).then(function(t){e.addressesRenderer.render(t.addresses)})}},{key:"_persistCartInfoData",value:function(e){this.data.cart_id=e.cart.id,this.data.delivery_address_id=e.cart.id_address_delivery,this.data.invoice_address_id=e.cart.id_address_invoice}},{key:"_choosePreviousCart",value:function(e){var t=this;R.ajax(this.$container.data("cart-summary-url"),{method:"POST",data:{id_cart:e,id_customer:this.data.customer_id},dataType:"json"}).then(function(e){t._persistCartInfoData(e),t._renderCartInfo(e)})}}]),e}();t.default=x},300:function(e,t){e.exports={base_url:"",routes:{admin_customers_carts:{tokens:[["text","/carts"],["variable","/","\\d+","customerId"],["text","/sell/customers"]],defaults:[],requirements:{customerId:"\\d+"},hosttokens:[],methods:["GET"],schemes:[]},admin_customers_orders:{tokens:[["text","/orders"],["variable","/","\\d+","customerId"],["text","/sell/customers"]],defaults:[],requirements:{customerId:"\\d+"},hosttokens:[],methods:["GET"],schemes:[]},admin_carts_info:{tokens:[["text","/info"],["variable","/","\\d+","cartId"],["text","/sell/orders/carts"]],defaults:[],requirements:{cartId:"\\d+"},hosttokens:[],methods:["GET"],schemes:[]},admin_carts_create:{tokens:[["text","/sell/orders/carts/new"]],defaults:[],requirements:[],hosttokens:[],methods:["POST"],schemes:[]},admin_carts_edit_address:{tokens:[["text","/edit-address"],["variable","/","\\d+","cartId"],["text","/sell/orders/carts"]],defaults:[],requirements:{cartId:"\\d+"},hosttokens:[],methods:["POST"],schemes:[]},admin_orders_duplicate_cart:{tokens:[["text","/duplicate-cart"],["variable","/","\\d+","orderId"],["text","/sell/orders/orders"]],defaults:[],requirements:{orderId:"\\d+"},hosttokens:[],methods:["POST"],schemes:[]}},prefix:"",host:"localhost",port:"",scheme:"http",locale:[]}},33:function(e,t,r){"use strict";Object.defineProperty(t,"__esModule",{value:!0}),/**
 * 2007-2019 PrestaShop SA and Contributors
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
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
t.default={orderCreationContainer:"#orderCreationContainer",customerSearchInput:"#customerSearchInput",customerSearchResultsBlock:".js-customer-search-results",customerSearchResultTemplate:"#customerSearchResultTemplate",changeCustomerBtn:".js-change-customer-btn",customerSearchRow:".js-search-customer-row",chooseCustomerBtn:".js-choose-customer-btn",notSelectedCustomerSearchResults:".js-customer-search-result:not(.border-success)",customerSearchResultName:".js-customer-name",customerSearchResultEmail:".js-customer-email",customerSearchResultId:".js-customer-id",customerSearchResultBirthday:".js-customer-birthday",customerDetailsBtn:".js-details-customer-btn",customerSearchResultColumn:".js-customer-search-result-col",customerSearchBlock:"#customerSearchBlock",customerCartsTable:"#customerCartsTable",customerCartsTableRowTemplate:"#customerCartsTableRowTemplate",customerCheckoutHistory:"#customerCheckoutHistory",customerOrdersTable:"#customerOrdersTable",customerOrdersTableRowTemplate:"#customerOrdersTableRowTemplate",vouchersTable:"#vouchersTable",vouchersTableRowTemplate:"#vouchersTableRowTemplate",cartBlock:"#cartBlock",vouchersBlock:"#vouchersBlock",addressesBlock:"#addressesBlock",deliveryAddressDetails:"#deliveryAddressDetails",invoiceAddressDetails:"#invoiceAddressDetails",deliveryAddressSelect:"#deliveryAddressSelect",invoiceAddressSelect:"#invoiceAddressSelect",addressSelect:".js-address-select",addressesContent:"#addressesContent",addressesWarning:"#addressesWarning",summaryBlock:"#summaryBlock",shippingBlock:"#shippingBlock"}},351:function(e,t,r){"use strict";var n=r(266),o=function(e){return e&&e.__esModule?e:{default:e}}(n);/**
                   * 2007-2019 PrestaShop SA and Contributors
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
                   * @copyright 2007-2019 PrestaShop SA and Contributors
                   * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
                   * International Registered Trademark & Property of PrestaShop SA
                   */
(0,window.$)(document).ready(function(){var e=new o.default;e.listenForCustomerSearch(),e.listenForCustomerSelect(),e.listenForCartSelect(),e.listenForOrderSelect(),e.listenForCartUpdate()})},352:function(e,t,r){"use strict";function n(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}Object.defineProperty(t,"__esModule",{value:!0});var o=function(){function e(e,t){for(var r=0;r<t.length;r++){var n=t[r];n.enumerable=n.enumerable||!1,n.configurable=!0,"value"in n&&(n.writable=!0),Object.defineProperty(e,n.key,n)}}return function(t,r,n){return r&&e(t.prototype,r),n&&e(t,n),t}}(),a=r(33),s=function(e){return e&&e.__esModule?e:{default:e}}(a),u=window.$,i=function(){function e(){n(this,e)}return o(e,[{key:"render",value:function(e){var t="",r="",n=u(s.default.deliveryAddressDetails),o=u(s.default.invoiceAddressDetails),a=u(s.default.deliveryAddressSelect),i=u(s.default.invoiceAddressSelect),c=u(s.default.addressesContent),d=u(s.default.addressesWarning);if(n.empty(),o.empty(),a.empty(),i.empty(),0===e.length)return d.removeClass("d-none"),void c.addClass("d-none");c.removeClass("d-none"),d.addClass("d-none");for(var l in Object.keys(e)){var f=e[l],h={value:f.addressId,text:f.alias},m={value:f.addressId,text:f.alias};f.delivery&&(t=f.formattedAddress,h.selected="selected"),f.invoice&&(r=f.formattedAddress,m.selected="selected"),a.append(u("<option>",h)),i.append(u("<option>",m))}t&&u(s.default.deliveryAddressDetails).html(t),r&&u(s.default.invoiceAddressDetails).html(r)}}]),e}();t.default=i},353:function(e,t,r){"use strict";function n(e){return e&&e.__esModule?e:{default:e}}function o(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}Object.defineProperty(t,"__esModule",{value:!0});var a=function(){function e(e,t){for(var r=0;r<t.length;r++){var n=t[r];n.enumerable=n.enumerable||!1,n.configurable=!0,"value"in n&&(n.writable=!0),Object.defineProperty(e,n.key,n)}}return function(t,r,n){return r&&e(t.prototype,r),n&&e(t,n),t}}(),s=r(33),u=n(s),i=r(70),c=n(i),d=window.$,l=function(){function e(){o(this,e),this.$container=d(u.default.orderCreationContainer),this.router=new c.default}return a(e,[{key:"getCart",value:function(e){return d.ajax(this.router.generate("admin_carts_info",{cartId:e}),{method:"GET"})}},{key:"loadEmptyCart",value:function(e){return d.ajax(this.router.generate("admin_carts_create"),{method:"POST",data:{customerId:e},dataType:"json"})}},{key:"duplicateOrderCart",value:function(e){return d.ajax(this.router.generate("admin_orders_duplicate_cart",{orderId:e}),{method:"POST"})}}]),e}();t.default=l},354:function(e,t,r){"use strict";function n(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}Object.defineProperty(t,"__esModule",{value:!0});var o=function(){function e(e,t){for(var r=0;r<t.length;r++){var n=t[r];n.enumerable=n.enumerable||!1,n.configurable=!0,"value"in n&&(n.writable=!0),Object.defineProperty(e,n.key,n)}}return function(t,r,n){return r&&e(t.prototype,r),n&&e(t,n),t}}(),a=r(33),s=function(e){return e&&e.__esModule?e:{default:e}}(a),u=window.$,i=function(){function e(){n(this,e)}return o(e,[{key:"render",value:function(e){var t=e.carts,r=e.currentCartId,n=u(s.default.customerCartsTable),o=u(u(s.default.customerCartsTableRowTemplate).html());if(n.find("tbody").empty(),t)for(var a in t){var i=t[a];if(i.cartId!==r){var c=o.clone();c.find(".js-cart-id").text(i.cartId),c.find(".js-cart-date").text(i.creationDate),c.find(".js-cart-total").text(i.totalPrice),c.find(".js-use-cart-btn").data("cart-id",i.cartId),n.find("tbody").append(c)}}}}]),e}();t.default=i},355:function(e,t,r){"use strict";function n(e){return e&&e.__esModule?e:{default:e}}function o(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}Object.defineProperty(t,"__esModule",{value:!0});var a=function(){function e(e,t){for(var r=0;r<t.length;r++){var n=t[r];n.enumerable=n.enumerable||!1,n.configurable=!0,"value"in n&&(n.writable=!0),Object.defineProperty(e,n.key,n)}}return function(t,r,n){return r&&e(t.prototype,r),n&&e(t,n),t}}(),s=r(33),u=n(s),i=r(70),c=n(i),d=window.$,l=function(){function e(){o(this,e),this.$container=d(u.default.orderCreationContainer),this.router=new c.default}return a(e,[{key:"getCustomerCarts",value:function(e){return d.ajax(this.router.generate("admin_customers_carts",{customerId:e}),{method:"GET"})}},{key:"getCustomerOrders",value:function(e){return d.ajax(this.router.generate("admin_customers_orders",{customerId:e}),{method:"GET"})}}]),e}();t.default=l},356:function(e,t,r){"use strict";function n(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}Object.defineProperty(t,"__esModule",{value:!0});var o=function(){function e(e,t){for(var r=0;r<t.length;r++){var n=t[r];n.enumerable=n.enumerable||!1,n.configurable=!0,"value"in n&&(n.writable=!0),Object.defineProperty(e,n.key,n)}}return function(t,r,n){return r&&e(t.prototype,r),n&&e(t,n),t}}(),a=r(33),s=function(e){return e&&e.__esModule?e:{default:e}}(a),u=window.$,i=function(){function e(){var t=this;return n(this,e),this.$container=u(s.default.customerSearchBlock),this.$searchInput=u(s.default.customerSearchInput),this.$customerSearchResultBlock=u(s.default.customerSearchResultsBlock),{onCustomerSearch:function(){t._doSearch()},onCustomerChooseForOrderCreation:function(e){return t._chooseCustomerForOrderCreation(e)},onCustomerChange:function(){t._showCustomerSearch()}}}return o(e,[{key:"_chooseCustomerForOrderCreation",value:function(e){var t=u(e.currentTarget),r=t.closest(".card");return t.addClass("d-none"),r.addClass("border-success"),r.find(s.default.changeCustomerBtn).removeClass("d-none"),this.$container.find(s.default.customerSearchRow).addClass("d-none"),this.$container.find(s.default.notSelectedCustomerSearchResults).closest(s.default.customerSearchResultColumn).remove(),t.data("customer-id")}},{key:"_doSearch",value:function(){var e=this,t=this.$searchInput.val();t.length<4||u.ajax(this.$searchInput.data("url"),{method:"GET",data:{customer_search:t}}).then(function(t){if(e._clearShownCustomers(),!t.found)return void e._showNotFoundCustomers();for(var r in t.customers){var n=t.customers[r],o={id:r,first_name:n.firstname,last_name:n.lastname,email:n.email,birthday:"0000-00-00"!==n.birthday?n.birthday:" "};e._showCustomer(o)}})}},{key:"_showCustomer",value:function(e){var t=u(u(s.default.customerSearchResultTemplate).html()),r=t.clone();return r.find(s.default.customerSearchResultName).text(e.first_name+" "+e.last_name),r.find(s.default.customerSearchResultEmail).text(e.email),r.find(s.default.customerSearchResultId).text(e.id),r.find(s.default.customerSearchResultBirthday).text(e.birthday),r.find(s.default.customerDetailsBtn).data("customer-id",e.id),r.find(s.default.chooseCustomerBtn).data("customer-id",e.id),this.$customerSearchResultBlock.append(r)}},{key:"_showNotFoundCustomers",value:function(){var e=u(u("#customerSearchEmptyResultTemplate").html());this.$customerSearchResultBlock.append(e)}},{key:"_clearShownCustomers",value:function(){this.$customerSearchResultBlock.empty()}},{key:"_showCustomerSearch",value:function(){this.$container.find(s.default.customerSearchRow).removeClass("d-none")}}]),e}();t.default=i},357:function(e,t,r){"use strict";function n(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}Object.defineProperty(t,"__esModule",{value:!0});var o=function(){function e(e,t){for(var r=0;r<t.length;r++){var n=t[r];n.enumerable=n.enumerable||!1,n.configurable=!0,"value"in n&&(n.writable=!0),Object.defineProperty(e,n.key,n)}}return function(t,r,n){return r&&e(t.prototype,r),n&&e(t,n),t}}(),a=r(33),s=function(e){return e&&e.__esModule?e:{default:e}}(a),u=window.$,i=function(){function e(){n(this,e)}return o(e,[{key:"render",value:function(e){var t=u(s.default.customerOrdersTable),r=u(u(s.default.customerOrdersTableRowTemplate).html());if(t.find("tbody").empty(),e)for(var n in Object.keys(e)){var o=e[n],a=r.clone();a.find(".js-order-id").text(o.orderId),a.find(".js-order-date").text(o.orderPlacedDate),a.find(".js-order-products").text(o.totalProductsCount),a.find(".js-order-total-paid").text(o.totalPaid),a.find(".js-order-status").text(o.orderStatus),a.find(".js-use-order-btn").data("order-id",o.orderId),t.find("tbody").append(a)}}}]),e}();t.default=i},358:function(e,t,r){"use strict";function n(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}Object.defineProperty(t,"__esModule",{value:!0});var o=function(){function e(e,t){for(var r=0;r<t.length;r++){var n=t[r];n.enumerable=n.enumerable||!1,n.configurable=!0,"value"in n&&(n.writable=!0),Object.defineProperty(e,n.key,n)}}return function(t,r,n){return r&&e(t.prototype,r),n&&e(t,n),t}}(),a=r(33),s=function(e){return e&&e.__esModule?e:{default:e}}(a),u=window.$,i=function(){function e(){n(this,e),this.$container=u(s.default.shippingBlock)}return o(e,[{key:"show",value:function(){this.$container.removeClass("d-none")}},{key:"hide",value:function(){this.$container.addClass("d-none")}}]),e}();t.default=i},359:function(e,t,r){"use strict";function n(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}Object.defineProperty(t,"__esModule",{value:!0});var o=function(){function e(e,t){for(var r=0;r<t.length;r++){var n=t[r];n.enumerable=n.enumerable||!1,n.configurable=!0,"value"in n&&(n.writable=!0),Object.defineProperty(e,n.key,n)}}return function(t,r,n){return r&&e(t.prototype,r),n&&e(t,n),t}}(),a=r(33),s=function(e){return e&&e.__esModule?e:{default:e}}(a),u=window.$,i=function(){function e(){n(this,e)}return o(e,[{key:"render",value:function(e){var t=u(s.default.vouchersTable),r=u(u(s.default.vouchersTableRowTemplate).html());if(t.find("tbody").empty(),0===e.length)return void t.addClass("d-none");t.removeClass("d-none");for(var n in e){var o=e[n],a=r.clone();a.find(".js-voucher-name").text(o.name),a.find(".js-voucher-description").text(o.description),a.find(".js-voucher-value").text(o.value),a.find(".js-voucher-delete-btn").data("cart-rule-id",o.cartRuleId),t.find("tbody").append(a)}}}]),e}();t.default=i},432:function(e,t,r){"use strict";function n(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}var o=Object.assign||function(e){for(var t,r=1;r<arguments.length;r++)for(var n in t=arguments[r])Object.prototype.hasOwnProperty.call(t,n)&&(e[n]=t[n]);return e},a="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(e){return typeof e}:function(e){return e&&"function"==typeof Symbol&&e.constructor===Symbol&&e!==Symbol.prototype?"symbol":typeof e},s=function e(){var t=this;n(this,e),this.setRoutes=function(e){t.routesRouting=e||[]},this.getRoutes=function(){return t.routesRouting},this.setBaseUrl=function(e){t.contextRouting.base_url=e},this.getBaseUrl=function(){return t.contextRouting.base_url},this.setPrefix=function(e){t.contextRouting.prefix=e},this.setScheme=function(e){t.contextRouting.scheme=e},this.getScheme=function(){return t.contextRouting.scheme},this.setHost=function(e){t.contextRouting.host=e},this.getHost=function(){return t.contextRouting.host},this.buildQueryParams=function(e,r,n){var o=new RegExp(/\[]$/);r instanceof Array?r.forEach(function(r,s){o.test(e)?n(e,r):t.buildQueryParams(e+"["+("object"===(void 0===r?"undefined":a(r))?s:"")+"]",r,n)}):"object"===(void 0===r?"undefined":a(r))?Object.keys(r).forEach(function(o){return t.buildQueryParams(e+"["+o+"]",r[o],n)}):n(e,r)},this.getRoute=function(e){var r=t.contextRouting.prefix+e;if(t.routesRouting[r])return t.routesRouting[r];if(!t.routesRouting[e])throw new Error('The route "'+e+'" does not exist.');return t.routesRouting[e]},this.generate=function(e,r,n){var a=t.getRoute(e),s=r||{},u=o({},s),i="_scheme",c="",d=!0,l="";if((a.tokens||[]).forEach(function(t){if("text"===t[0])return c=t[1]+c,void(d=!1);if("variable"!==t[0])throw new Error('The token type "'+t[0]+'" is not supported.');var r=(a.defaults||{})[t[3]];if(0==d||!r||(s||{})[t[3]]&&s[t[3]]!==a.defaults[t[3]]){var n;if((s||{})[t[3]])n=s[t[3]],delete u[t[3]];else{if(!r){if(d)return;throw new Error('The route "'+e+'" requires the parameter "'+t[3]+'".')}n=a.defaults[t[3]]}if(!(!0===n||!1===n||""===n)||!d){var o=encodeURIComponent(n).replace(/%2F/g,"/");"null"===o&&null===n&&(o=""),c=t[1]+o+c}d=!1}else r&&delete u[t[3]]}),""==c&&(c="/"),(a.hosttokens||[]).forEach(function(e){var t;return"text"===e[0]?void(l=e[1]+l):void("variable"===e[0]&&((s||{})[e[3]]?(t=s[e[3]],delete u[e[3]]):a.defaults[e[3]]&&(t=a.defaults[e[3]]),l=e[1]+t+l))}),c=t.contextRouting.base_url+c,a.requirements[i]&&t.getScheme()!==a.requirements[i]?c=a.requirements[i]+"://"+(l||t.getHost())+c:l&&t.getHost()!==l?c=t.getScheme()+"://"+l+c:!0===n&&(c=t.getScheme()+"://"+t.getHost()+c),0<Object.keys(u).length){var f=[],h=function(e,t){var r=t;r="function"==typeof r?r():r,r=null===r?"":r,f.push(encodeURIComponent(e)+"="+encodeURIComponent(r))};Object.keys(u).forEach(function(e){return t.buildQueryParams(e,u[e],h)}),c=c+"?"+f.join("&").replace(/%20/g,"+")}return c},this.setData=function(e){t.setBaseUrl(e.base_url),t.setRoutes(e.routes),"prefix"in e&&t.setPrefix(e.prefix),t.setHost(e.host),t.setScheme(e.scheme)},this.contextRouting={base_url:"",prefix:"",host:"",scheme:""}};e.exports=new s},70:function(e,t,r){"use strict";function n(e){return e&&e.__esModule?e:{default:e}}function o(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}Object.defineProperty(t,"__esModule",{value:!0});var a=function(){function e(e,t){for(var r=0;r<t.length;r++){var n=t[r];n.enumerable=n.enumerable||!1,n.configurable=!0,"value"in n&&(n.writable=!0),Object.defineProperty(e,n.key,n)}}return function(t,r,n){return r&&e(t.prototype,r),n&&e(t,n),t}}(),s=r(432),u=n(s),i=r(300),c=n(i),d=window.$,l=function(){function e(){return o(this,e),u.default.setData(c.default),u.default.setBaseUrl(d(document).find("body").data("base-url")),this}return a(e,[{key:"generate",value:function(e){var t=arguments.length>1&&void 0!==arguments[1]?arguments[1]:{},r=Object.assign(t,{_token:d(document).find("body").data("token")});return u.default.generate(e,r)}}]),e}();t.default=l}});
