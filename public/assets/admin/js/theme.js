/*
 * ATTENTION: The "eval" devtool has been used (maybe by default in mode: "development").
 * This devtool is neither made for production nor for readable output files.
 * It uses "eval()" calls to create a separate source file in the browser devtools.
 * If you are trying to read the output file, select a different devtool (https://webpack.js.org/configuration/devtool/)
 * or disable the default devtool with "devtool: false".
 * If you are looking for production-ready output files, see mode: "production" (https://webpack.js.org/configuration/mode/).
 */
/******/ (() => { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ "./admin/js/backend.js":
/*!*****************************!*\
  !*** ./admin/js/backend.js ***!
  \*****************************/
/***/ (() => {

eval("$(document).ready(function () {\n  $(document).on('change', '.shop-service-selected', function () {\n    if ($(this).is(':checked')) {\n      $(this).closest('.service-group').find('.shop-service-selected-price').removeClass('d-none');\n    } else {\n      $(this).closest('.service-group').find('.shop-service-selected-price').addClass('d-none');\n    }\n  });\n});\n\n//# sourceURL=webpack://salon-booking/./admin/js/backend.js?");

/***/ }),

/***/ "./admin/css/theme.scss":
/*!******************************!*\
  !*** ./admin/css/theme.scss ***!
  \******************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n// extracted by mini-css-extract-plugin\n\n\n//# sourceURL=webpack://salon-booking/./admin/css/theme.scss?");

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The require scope
/******/ 	var __webpack_require__ = {};
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	(() => {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = (exports) => {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	})();
/******/ 	
/************************************************************************/
/******/ 	
/******/ 	// startup
/******/ 	// Load entry module and return exports
/******/ 	// This entry module can't be inlined because the eval devtool is used.
/******/ 	__webpack_modules__["./admin/js/backend.js"](0, {}, __webpack_require__);
/******/ 	var __webpack_exports__ = {};
/******/ 	__webpack_modules__["./admin/css/theme.scss"](0, __webpack_exports__, __webpack_require__);
/******/ 	
/******/ })()
;