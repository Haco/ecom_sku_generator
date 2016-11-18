// Centers Checkboxes in PartSelector
function equalizeHeight() {
    $('#ccg-generator-canvas').find('.generator-select-part-group-part').each(function () {
        var checkboxHeight = $('.generator-checkbox', this).outerHeight();
        var partSelectorHeight = $(this).height();
        $('.generator-checkbox', this).css('margin-top', (partSelectorHeight / 2) - (checkboxHeight / 2));
    });
}
equalizeHeight();
$(window).resize(function () {
    equalizeHeight();
});

// Review/Summary Configuration Button
(function ($) {
    var summaryTable = $('#generator-summary-table');
    $('#ccg-generator-canvas').find('.generator-result-review-config').on('click', function (e) {
        // Prevent default anchor action
        e.preventDefault();
        $(this).stop().toggleClass('active');
        summaryTable.stop().slideToggle('slow').toggleClass('active');

        // Scroll in position if the table is not currently hidden
        if (summaryTable.hasClass('active')) {
            $('html, body').stop().animate({
                scrollTop: summaryTable.offset().top
            }, 'slow');
        }
    });
})(jQuery);

/**
 * ajax.js
 */
function addAjaxLoader(element) {
    $('#' + element).addClass('ajaxloader');
}

function removeAjaxLoader(element) {
    $('#' + element).removeClass('ajaxloader');
}

/**************************************
 *                                    *
 * AJAX request functions (re-worked) *
 *                                    *
 *************************************/

/**
 * Update part function
 */
function ccgUpdatePart() {
    $('.generator-select-part-group-part-selector').on('click', function (e) {
        e.preventDefault();
        if ($(this).hasClass('disabled')) {
            $(this).blur();
            return void(0);
        }
        var part = $(this).attr('data-part'),
            unset = $(this).attr('data-part-state');
        ccgUpdatePartExec(part, unset);
    });
}

function ccgUpdatePartExec(part, unset) {
    addAjaxLoader('ccg-generator-ajax-loader');
    genericAjaxRequest(t3pid, t3lang, 1444800649, 'updatePart', {
        part: part,
        unset: unset,
        cObj: t3cobj
    }, function (result) {
        onSuccessFunction(result);
    });
}

/**
 * Update index view only (switch between packages)
 */
function ccgIndex(target) {
    $(target).on('click', function (e) {
        // Prevent default anchor action
        e.preventDefault();
        /*if ( target === "#generator-next-button" && $('.generator-part-group-select[data-part-group=]').hasClass('disabled') ) {
         $(this).blur();
         return void(0);
         }*/
        if (( $(this).hasClass('generator-part-group-state-0') && $('.generator-part-group-state-0').first().attr('id') !== $(this).attr('id')) || $(this).hasClass('generator-locked-part-group') || $(this).hasClass('current')) {
            $(this).blur();
            return false;
        }
        addAjaxLoader('ccg-generator-ajax-loader');
        genericAjaxRequest(t3pid, t3lang, 1444800649, 'index', {
            partGroup: $(this).attr('data-part-group'),
            cObj: t3cobj
        }, function (result) {
            onSuccessFunction(result);
        });
    });
}

/**
 * @param part
 * @returns {boolean}
 */
function getPartInformation(part) {
    genericAjaxRequest(t3pid, t3lang, 1444800649, 'showHint', {
            part: part,
            cObj: t3cobj
        }, function (result) {
            makeBootstrapModal({
                'title': result['partTitle'],
                'body': result['partHint']
            });
        }
    );
    return false;
}

/**
 * Generic AJAX request function
 *
 * @param pageUid
 * @param language
 * @param pageType
 * @param action
 * @param arguments
 * @param onSuccess
 */
function genericAjaxRequest(pageUid, language, pageType, action, arguments, onSuccess) {
    $.ajax({
        async: 'true',
        url: 'index.php',
        type: 'POST',
        //contentType: 'application/json; charset=utf-8',
        dataType: 'json',
        data: {
            eID: 'EcomSkuGenerator',
            id: parseInt(pageUid),
            L: parseInt(language),
            type: parseInt(pageType),
            request: {
                controllerName: 'AjaxRequest',
                actionName: action,
                arguments: arguments
            }
        },
        success: onSuccess,
        error: function (jqXHR, textStatus, errorThrown) {
            console.log('Request failed with ' + textStatus + ': ' + errorThrown + '!');
        }
    });
}

/**
 * Handle the successful ajax request when a part got selected and handle final result when the end is reached
 * @param result
 */
function onSuccessFunction(result) {
    var resetButton = $('#generator-reset-configuration-button'),
        nextButton = $('#generator-next-button'),
        confPrice = $('#generator-config-header-config-price');
    removeAjaxLoader('ccg-generator-ajax-loader');
    updateProgressIndicator(result.progress);
    resetButton.toggle(!result.showResultingConfiguration && result.progress > 0);
    $('#generator-part-group-select-index').html(result.selectPartGroupsHTML);
    $('#generator-select-parts-ajax-update').html(result.selectPartsHTML);
    $('.js-eskuc-show-configuration-price').html(result.configurationPrice);
    if (confPrice) {
        confPrice.html(result.configurationPrice);
    }
    if (result.showResultingConfiguration) {
        $('#generator-result-canvas').show();
        $('#generator-part-group-select-part-index').hide();
        alterPartGroupInformation('hide');
        $('#generator-show-result-button').hide();
        nextButton.hide();
        nextButton.attr('data-current', 0);
        $('#generator-result-canvas .generator-result h3.generator-result-label').first().html(result.title);
        $('#generator-result-canvas .generator-result small.generator-result-code').first().html(result.configurationCode['code']);
        $('#generator-summary-table').html(result.configurationCode['summaryTable']);
        $('#generator-min-order-quantity').html(result.configurationCode['minOrderQuantityHtml']);
    } else {
        $('#generator-result-canvas').hide();
        $('#generator-part-group-select-part-index').show().promise().done(function () {
            equalizeHeight();
        });
        alterPartGroupInformation(result.currentPartGroup);
        $('#generator-show-result-button').toggle(result.progress === 1 && !result.currentPartGroup['last']);
        nextButton.attr('data-part-group', result.nextPartGroup);
        nextButton.attr('data-current', result.currentPartGroup ? result.currentPartGroup['uid'] : 0);
        if (result.currentPartGroup && $('#generator-part-group-' + result.currentPartGroup['uid'] + '-link').hasClass('generator-part-group-state-1')) {
            nextButton.removeClass('disabled btn-default').addClass('btn-primary');
        } else {
            nextButton.addClass('disabled btn-default').removeClass('btn-primary');
        }
        nextButton.show();
        if (result.progress === 0) {
            resetButton.addClass('disabled');
        } else {
            resetButton.removeClass('disabled');
        }
    }
    assignListeners(result);
    $('#ccg-generator-canvas').scrollTop();
}

/**********************************
 * Various build helper functions *
 *********************************/

/**
 * Alter part group information
 * @param data
 */
function alterPartGroupInformation(data) {
    var div = $('#generator-part-group-info');
    switch (data) {
        case 'show':
            div.show();
            break;
        case 'hide':
            div.hide();
            break;
        default:
            if (data instanceof Object) {
                /* Add dependency notes */
                var addDN = data.dependentNotesFluidParsedMessages !== undefined ? data.dependentNotesFluidParsedMessages : '';
                div.html('<h2>' + data.title + '</h2><p>' + data.prompt + '</p><p>' + addDN + '</p>').show();
            }
    }
}

/**
 * Update progress indicators including progress bar and 'percent done' display
 * @param progress
 */
function updateProgressIndicator(progress) {
    // Update/animate progress bar
    $('#generator-progress-value').animate({value: progress}, 800);
    // Update progress width for older browsers
    $('.ccg-generator-progress-wrap .progress-bar > span').animate({width: progress * 100 + '%'}, 800);
    // Update/animate number display(s)
    $('.generator-progress-value-print').each(function (index, element) {
        $({countNum: $(element).text()}).animate({countNum: Math.floor(progress * 100)}, {
            duration: 800,
            easing: 'linear',
            step: function () {
                $(element).text(Math.floor(this.countNum));
            },
            complete: function () {
                $(element).text(this.countNum);
            }
        });
    });
}

// Popup on click
function addInfoTrigger() {
    var triggerHint = '#ccg-generator-canvas .generator-select-part-group-part-info';
    var triggerPreviewImage = '#ccg-generator-canvas .generator-select-part-group-part-image';

    $(triggerHint).on('click', function (e) {
        e.preventDefault();
        getPartInformation($(this).parents('a').first().attr('data-part'));
        return false;
    });

    $(triggerPreviewImage).on('click', function (e) {
        e.preventDefault();
        return false;
    }).popover({
        html: true,
        trigger: 'hover',
        placement: 'left',
        content: function () {
            return '<img width="200px" alt="Preview Image" src="' + $(this).data('image-src') + '" />';
        }
    }).on('click', function () {
        $(this).popover('toggle');
    }).on('shown.bs.popover', function () {
        $('#' + $(this).attr('aria-describedby')).on('click', function (e) {
            e.preventDefault();
            return false;
        });
    });
}

/**
 * Add default listeners
 */
function assignListeners() {
    ccgUpdatePart();

    // Init BS Tooltips
    $('[data-toggle="tooltip"]').tooltip();

    addInfoTrigger();
    ccgIndex('.generator-part-group-select');
}

/**
 * Build Bootstrap Modal
 */
function makeBootstrapModal(modalData, part, unset, confirmModal) {
    confirmModal = typeof confirmModal !== 'undefined' ? confirmModal : false;

    var $CCGModal = $('.ccg-base-modal');
    var $ModalTitle = $CCGModal.find('.ccg-base-modal-title').empty();
    var $ModalBody = $CCGModal.find('.ccg-base-modal-body').empty();
    var $ModalFooter = $CCGModal.find('.ccg-base-modal-footer').empty();

    $ModalTitle.html('<span class="fa fa-info-circle fa-fw space-right"></span>' + ' ' + modalData['title']);
    $ModalBody.html(modalData['body']);

    if (confirmModal) {
        $ModalFooter.append('<button type="button" class="btn btn-danger text-uppercase" data-dismiss="modal">Cancel <span class="fa fa-close fa-fw"></span></button>');
        $ModalFooter.append('<button type="button" class="btn btn-success text-uppercase" id="ccg-modal-confirm">Proceed <span class="fa fa-chevron-right fa-fw"></span></button>');
        var $ModalConfirm = $ModalFooter.find('#ccg-modal-confirm');

        $ModalConfirm.on('click', function () {
            ccgUpdatePartExec(part, unset);
            $CCGModal.modal('hide');
        });
    } else {
        $ModalFooter.append('<button type="button" class="btn btn-primary text-uppercase" data-dismiss="modal">OK <span class="fa fa-check fa-fw"></span></button>');
    }

    $CCGModal.modal('show');
}

/**********************************************
 * Initialize Event Listeners once DOM loaded *
 *********************************************/
(function () {
    $('#generator-result-canvas').toggle(showResult);
    $('#generator-reset-configuration-button').toggle(!showResult);
    $('#generator-next-button').toggle(!showResult);
    $('#generator-part-group-select-part-index').toggle(!showResult);
    $('#generator-show-result-button').hide();
    assignListeners();
    ccgIndex('#generator-next-button');
})(jQuery);