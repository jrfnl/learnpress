/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, { enumerable: true, get: getter });
/******/ 		}
/******/ 	};
/******/
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = function(exports) {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/
/******/ 	// create a fake namespace object
/******/ 	// mode & 1: value is a module id, require it
/******/ 	// mode & 2: merge all properties of value into the ns
/******/ 	// mode & 4: return value when already ns object
/******/ 	// mode & 8|1: behave like require
/******/ 	__webpack_require__.t = function(value, mode) {
/******/ 		if(mode & 1) value = __webpack_require__(value);
/******/ 		if(mode & 8) return value;
/******/ 		if((mode & 4) && typeof value === 'object' && value && value.__esModule) return value;
/******/ 		var ns = Object.create(null);
/******/ 		__webpack_require__.r(ns);
/******/ 		Object.defineProperty(ns, 'default', { enumerable: true, value: value });
/******/ 		if(mode & 2 && typeof value != 'string') for(var key in value) __webpack_require__.d(ns, key, function(key) { return value[key]; }.bind(null, key));
/******/ 		return ns;
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";
/******/
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = "./assets/src/js/admin/admin.js");
/******/ })
/************************************************************************/
/******/ ({

/***/ "./assets/src/js/admin/admin.js":
/*!**************************************!*\
  !*** ./assets/src/js/admin/admin.js ***!
  \**************************************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _partial_update__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./partial/update */ "./assets/src/js/admin/partial/update.js");
/* harmony import */ var _partial_update__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_partial_update__WEBPACK_IMPORTED_MODULE_0__);
/**
 * JS code may run in all pages in admin.
 *
 * @version 3.2.6
 */

var LP = LP || {};

(function () {
  var $ = jQuery;

  var updateItemPreview = function updateItemPreview() {
    $.ajax({
      url: '',
      data: {
        'lp-ajax': 'toggle_item_preview',
        item_id: this.value,
        previewable: this.checked ? 'yes' : 'no',
        nonce: $(this).attr('data-nonce')
      },
      dataType: 'text',
      success: function success(response) {
        response = LP.parseJSON(response);
      }
    });
  };
  /**
   * Callback event for button to creating pages inside error message.
   *
   * @param {Event} e
   */


  var createPages = function createPages(e) {
    var $button = $(this).addClass('disabled');
    e.preventDefault();
    $.post({
      url: $button.attr('href'),
      data: {
        'lp-ajax': 'create-pages'
      },
      dataType: 'text',
      success: function success(res) {
        var $message = $button.closest('.lp-notice').html('<p>' + res + '</p>');
        setTimeout(function () {
          $message.fadeOut();
        }, 2000);
      }
    });
  };

  var hideUpgradeMessage = function hideUpgradeMessage(e) {
    e.preventDefault();
    var $btn = $(this);
    $btn.closest('.lp-upgrade-notice').fadeOut();
    $.post({
      url: '',
      data: {
        'lp-hide-upgrade-message': 'yes'
      },
      success: function success(res) {}
    });
  };

  var pluginActions = function pluginActions(e) {
    // Premium addon
    if ($(e.target).hasClass('buy-now')) {
      return;
    }

    e.preventDefault();
    var $plugin = $(this).closest('.plugin-card');

    if ($(this).hasClass('updating-message')) {
      return;
    }

    $(this).addClass('updating-message button-working disabled');
    $.ajax({
      url: $(this).attr('href'),
      data: {},
      success: function success(r) {
        $.ajax({
          url: window.location.href,
          success: function success(r) {
            var $p = $(r).find('#' + $plugin.attr('id'));

            if ($p.length) {
              $plugin.replaceWith($p);
            } else {
              $plugin.find('.plugin-action-buttons a').removeClass('updating-message button-working').html(learn_press_admin_localize.plugin_installed);
            }
          }
        });
      }
    });
  };

  var preventDefault = function preventDefault(e) {
    e.preventDefault();
    return false;
  };

  var ajaxCreateQuestionType = function ajaxCreateQuestionType(e) {
    var type = $(e.target).data('type') || $(this).find('li:first').data('type');
    var ajaxUrl = window['lpAdminSettings'].ajax;
    $.ajax({
      url: ajaxUrl,
      data: {
        'lp-ajax': 'create-question-type',
        type: type
      }
    });
  };

  LP.createButtonAddNewQuestion = function () {
    if (!$(document.body).hasClass('post-type-lp_question')) {
      return;
    }

    var $addNew = $(document).find('.page-title-action');

    if (!$addNew.length) {
      return;
    }

    var types = window['lpAdminSettings'].questionTypes;
    var $newButton = $('<div id="button-new-question" class="page-title-action"><div></div></div>');
    var url = $addNew.attr('href');

    for (var type in types) {
      $newButton.find('div').append("<a href=\"".concat(url.addQueryVar('question-type', type), "\">").concat(types[type], "</a>"));
    }

    $newButton.find('span').html($addNew.text()); //$addNew.append($newButton);

    $newButton.insertBefore($addNew);
    $newButton.prepend($addNew.removeClass('page-title-action'));
  };
  /**
   * Auto focus into the last item in Text list after user press 'Add more'
   * button.
   *
   * If there is an empty item then focus it and remove the new item added.
   *
   * @param event
   */


  var focusToInputWhenCloningTextList = function focusToInputWhenCloningTextList(event) {
    setTimeout(function () {
      var $container = $(event.target).closest('.rwmb-text-list-wrapper');
      var $siblings = $container.find('.rwmb-text-list-clone');
      var $lastRow = $siblings.last();
      $siblings.each(function () {
        var $row = $(this);

        if (!$row.find('.rwmb-text-list').val().length) {
          $row.find('.rwmb-text-list').focus();

          if (!$row.is($lastRow)) {
            $lastRow.remove();
          }

          return false;
        }
      });
    }, 20);
  };
  /**
   * Add new option to Metabox Text list when user press Enter in
   * an existing item.
   *
   * @param event
   */


  var addOptionToTextList = function addOptionToTextList(event) {
    if (event.keyCode !== 13) {
      return;
    }

    event.preventDefault();
    var $item = $(event.target).closest('.rwmb-text-list-clone');
    var $container = $(event.target).closest('.rwmb-text-list-wrapper');
    var $siblings = $container.find('.rwmb-text-list-clone');

    if ($siblings.last().is($item)) {
      $container.find('.add-clone').trigger('click');
    } else {
      $(event.target).closest('.rwmb-text-list-clone').next().find('.rwmb-text-list').focus();
    }
  };

  var autoCheckHideContentOption = function autoCheckHideContentOption(event) {
    var isChecked = $(event.target).is(':checked');

    if (!isChecked) {
      return;
    }

    $('#_lp_block_content').prop('checked', true);
  };

  var onReady = function onReady() {
    $('.learn-press-dropdown-pages').LP('DropdownPages');
    $('.learn-press-advertisement-slider').LP('Advertisement', 'a', 's').appendTo($('#wpbody-content'));
    $('.learn-press-toggle-item-preview').on('change', updateItemPreview);
    $('.learn-press-tip').LP('QuickTip'); //$('.learn-press-tabs').LP('AdminTab');

    LP.createButtonAddNewQuestion();
    $(document).on('click', '#learn-press-create-pages', createPages).on('click', '.lp-upgrade-notice .close-notice', hideUpgradeMessage).on('click', '.plugin-action-buttons a', pluginActions).on('click', '[data-remove-confirm]', preventDefault).on('mousedown', '.lp-sortable-handle', function (e) {
      $('html, body').addClass('lp-item-moving');
      $(e.target).closest('.lp-sortable-handle').css('cursor', 'inherit');
    }).on('mouseup', function (e) {
      $('html, body').removeClass('lp-item-moving');
      $('.lp-sortable-handle').css('cursor', '');
    }).on('click', '.rwmb-text-list-wrapper .add-clone', focusToInputWhenCloningTextList).on('keypress', '.rwmb-text-list-wrapper .rwmb-text-list', addOptionToTextList).on('change', '#_lp_retake_count', autoCheckHideContentOption);
    setTimeout(function () {
      $('.rwmb-text-list-wrapper .rwmb-input').removeClass('ui-sortable').sortable({
        axis: 'y',
        handle: 'label'
      });
    }, 100);
    $(document).on('LP.adminTabs.selectTab', function (event, $tab, url) {
      if ($(document).find('input[name="post_type"]').val() === 'lp_course') {
        $('input[name="_wp_http_referer"], input[name="referredby"], input[name="_wp_original_http_referer"]').val(url);
      }
    });
  };

  $(document).ready(onReady);
})();

/***/ }),

/***/ "./assets/src/js/admin/partial/update.js":
/*!***********************************************!*\
  !*** ./assets/src/js/admin/partial/update.js ***!
  \***********************************************/
/*! no static exports found */
/***/ (function(module, exports) {

;

(function ($) {
  'use strict';

  var Package = function Package(data) {
    this.data = data;
    var currentIndex = -1,
        currentVersion = null,
        currentPackage = null,
        versions = Object.keys(this.data);

    this.reset = function (current) {
      current = current === undefined || current > versions.length - 1 || current < 0 ? 0 : current;
      currentIndex = current;
      currentVersion = versions[current];
      currentPackage = this.data[currentVersion];
      return currentPackage;
    };

    this.next = function () {
      if (currentIndex >= versions.length - 1) {
        return false;
      }

      currentIndex++;
      this.reset(currentIndex);
      return currentPackage;
    };

    this.prev = function () {
      if (currentIndex <= 0) {
        return false;
      }

      currentIndex--;
      this.reset(currentIndex);
      return currentPackage;
    };

    this.currentVersion = function () {
      return currentVersion;
    };

    this.hasPackage = function () {
      return versions.length;
    };

    this.getPercentCompleted = function () {
      return currentIndex / versions.length;
    };

    this.getTotal = function () {
      return versions.length;
    };

    if (!this.data) {
      return;
    }
  };

  var UpdaterSettings = {
    el: '#learn-press-updater',
    data: {
      packages: null,
      status: '',
      force: false
    },
    watch: {
      packages: function packages(newPackages, oldPackages) {
        if (newPackages) {}
      }
    },
    mounted: function mounted() {
      $(this.$el).show();
    },
    methods: {
      getUpdatePackages: function getUpdatePackages(callback) {
        var that = this;
        $.ajax({
          url: lpGlobalSettings.admin_url,
          data: {
            'lp-ajax': 'get-update-packages',
            force: this.force,
            _wpnonce: lpGlobalSettings._wpnonce
          },
          success: function success(res) {
            var packages = LP.parseJSON(res);
            that.packages = new Package(packages);
            callback && callback.call(that);
          }
        });
      },
      start: function start(e, force) {
        this.packages = null;
        this.force = force;
        this.getUpdatePackages(function () {
          if (this.packages.hasPackage()) {
            var p = this.packages.next();
            this.status = 'updating';
            this.doUpdate(p);
          }
        });
      },
      getPackages: function getPackages() {
        return this.packages ? this.packages.data : {};
      },
      hasPackage: function hasPackage() {
        return !$.isEmptyObject(this.getPackages());
      },
      updateButtonClass: function updateButtonClass() {
        return {
          'disabled': this.status === 'updating'
        };
      },
      doUpdate: function doUpdate(p, i) {
        var that = this;
        p = p ? p : this.packages.next();
        i = i ? i : 1;

        if (p) {
          $.ajax({
            url: lpGlobalSettings.admin_url,
            data: {
              'lp-ajax': 'do-update-package',
              "package": p,
              version: this.packages.currentVersion(),
              _wpnonce: lpGlobalSettings._wpnonce,
              force: this.force,
              i: i
            },
            success: function success(res) {
              var response = LP.parseJSON(res),
                  $status = $(that.$el).find('.updater-progress-status');

              if (response.done === 'yes') {
                that.update(that.packages.getPercentCompleted() * 100);
                that.doUpdate();
              } else {
                var newWidth = that.packages.getPercentCompleted() * 100;

                if (response.percent) {
                  var stepWidth = 1 / that.packages.getTotal();
                  newWidth += stepWidth * response.percent;
                }

                that.update(newWidth);
                that.doUpdate(p, ++i);
              }
            },
            error: function error() {
              that.doUpdate(p, i);
            }
          });
        } else {
          that.update(100).addClass('completed');
          setTimeout(function (x) {
            x.status = 'completed';
          }, 2000, this);
        }
      },
      update: function update(value) {
        return $(this.$el).find('.updater-progress-status').css('width', value + '%').attr('data-value', parseInt(value));
      }
    }
  };

  function init() {
    window.lpGlobalSettings = window.lpGlobalSettings || {};

    if ($('#learn-press-updater').length) {
      var Updater = new Vue(UpdaterSettings);
    }

    var i18n = window.lpUpdateSettings || {};
    $(document).on('click', '#button-update', function (e) {
      e.preventDefault();
      var $form = $('#learn-press-update-form'),
          loadUrl = $(this).attr('href'),
          $main = $('#main').addClass('loading');
      $('.learn-press-message').remove();
      $.post({
        url: loadUrl,
        data: $form.serializeJSON(),
        success: function success(res) {
          $(res).insertBefore($form);
          $main.removeClass('loading');
        }
      });
    }).on('click', '.lp-button-upgrade', function (e) {
      e.preventDefault();

      if (!confirm(i18n.i18n_confirm)) {
        return false;
      }

      var $btn = $(this),
          url = $btn.addClass('disabled').attr('href'),
          context = $btn.data('context');
      $.post({
        url: url,
        data: {
          context: context
        },
        success: function success(res) {
          var $msg = $(res);

          if (context == 'message') {
            $btn.closest('.notice').replaceWith($msg);
          } else {
            $msg.insertBefore($btn);
          }
        }
      });
    }).on('click', '#skip-notice-install', function () {
      $.post({
        url: '',
        data: {
          'lp-ajax': 'skip-notice-install'
        }
      });
      $('#notice-install').fadeOut();
    });
  }

  $(document).ready(init);
})(jQuery);

/***/ })

/******/ });
//# sourceMappingURL=admin.js.map