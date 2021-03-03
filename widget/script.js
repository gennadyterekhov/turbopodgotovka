gigantSearchNearestShops = function () {
    var widget = this;
    this.code = null;
    // тест или прод
    this.isTest = false;
    this.ignoreFull = true;
    this.sortDirection = true;
    // префикс для селекторов
    this.widgetPrefix = 'gigantSearchNearestShops';

    // хардкод id кастомных полей клиента
    this.fieldsConfig = {
        introverttest: {
            'Магазин квал': 966107,
            'Клиент': 966279,
            'Желаемая вакансия': 946725,
            'Желаемое расстояние': 938971,
            'Адрес проживания 1': 938973,
            'ГороД': 938975,
        },
        client: {
            'Магазин квал': 673178,
            'Клиент': 670010,
            'Желаемая вакансия': 669898,
            'Желаемое расстояние': 673180,
            'Адрес проживания 1': 662511,
            'ГороД': 657485,
        }
    };

    this.defaultFieldsData = {
        'Магазин квал': null,
        'Клиент': null,
        'Желаемая вакансия': null,
        'Желаемое расстояние': null,
        'Адрес проживания 1': null,
        'ГороД': 'Москва',
    }

    this.config = {
        fields: (this.isTest) ? widget.fieldsConfig.introverttest : widget.fieldsConfig.client,
        baseUrl: (this.isTest) ? 'https://test.dev.introvert.bz/gigant/search_nearest_shops/' : 'https://dev.introvert.bz/gigant/search_nearest_shops/',
        // api ключ гугл карт
        googleMapsApiKeyTest: 'AIzaSyCtCgnDAAC2JqVax-Yc2LplcaLXo0VWCgA',
        googleMapsApiKey: 'AIzaSyCMbQV_qujxAW1ZqpoCnuR2A38eYxuhICU',
    };

    
    // селекторы
    this.selectors = {
        // id кнопок
        availableShopsBtnId: `${widget.widgetPrefix}-available-shops-btn`,
        loadShopsBtnId: `${widget.widgetPrefix}-modal-load-shops-btn`,
        searchBtnId: `${widget.widgetPrefix}-modal-search-btn`,
        sortBtnId: `${widget.widgetPrefix}-modal-sort-btn`,
        limitFastInput: {
            '3': `${widget.widgetPrefix}-modal-limit-fast-input-3-btn`,
            '5': `${widget.widgetPrefix}-modal-limit-fast-input-5-btn`,
            '10': `${widget.widgetPrefix}-modal-limit-fast-input-10-btn`,
        },
        // id элементов мод окна
        modalId: `${widget.widgetPrefix}-modal`,
        modalClass: `${widget.widgetPrefix}-modal`,
        modalFormId: `${widget.widgetPrefix}-modal-form`,
        modalTableId: `${widget.widgetPrefix}-modal-table`,
        modalAddressId: `${widget.widgetPrefix}-modal-address`,
        modalLimitId: `${widget.widgetPrefix}-modal-limit`,
        modalSearchShopsId: `${widget.widgetPrefix}-modal-search-shops`,
        modalSearchVacanciesId: `${widget.widgetPrefix}-modal-search-vacancies`,
        ignoreFullCheckboxId: `${widget.widgetPrefix}-modal-ignore-full`,
        // доп классы
        clickableVacancyClass: `${widget.widgetPrefix}-vacancy-name-clickable`,
        modalCloseClass: `icon-modal-close`,
        // селекторы для вставки
        loadShopsBtnAppend: `#${widget.widgetPrefix}-modal-form`,
        availableShopsBtnAfter: '.card-tabs-wrapper',
    };
   




    // хардкод css
    this.css = {
        sortBtnStyle: `
        <style>
            button.btn--link-icon {
                border: 1px solid #DFE2E3;
                background: rgba(0, 0, 0, 0);
            }

            .btn--link-icon {
                    border-radius: 5px;
                    height: 20px;
                    width: 20px;
                    padding: 7px;
            }
            .pointer_down:after {
                content: "";
                position: absolute;
                top: calc(50% - 5px);
                width: 6px;
                height: 6px;
                border-bottom: 1px solid #555;
                border-right: 1px solid #555;
                -webkit-transform: rotate(45deg);
                transform: rotate(45deg);
                margin-left: 7px;
                right: 11px;
                z-index: -1;
            }
            .btn--pointer_up .pointer_down:after {
                transition: .6s;
                transform: rotate(225deg) translate(-22%, -36%);
            }
        </style>
        `,
    };


    // хардкод html
    this.html = {
        sortBtnHtmlDown: `
        <button class="button-input trigger--pointer_anim btn--link-icon" id="${widget.selectors.sortBtnId}">
            <div class="pointer_down"></div>
        </button>
        ${widget.css.sortBtnStyle}
        `,
        // sortBtnHtmlDown: `
        //     <div class="pointer_down" id="${widget.selectors.sortBtnId}"></div>
        // ${widget.css.sortBtnStyle}
        // `,
        sortBtnHtmlUp: `
        <button class="button-input trigger--pointer_anim btn--link-icon btn--pointer_up" id="${widget.selectors.sortBtnId}">
            <div class="pointer_down "></div>
        </button>
        ${widget.css.sortBtnStyle}
        `,
        // sortBtnHtmlUp: `
        //     <div class="pointer_down " id="${widget.selectors.sortBtnId}"></div>
        // ${widget.css.sortBtnStyle}
        // `,
    };


    // методы для получения/подсчета/конвертации 
    this.getters = {
        // получить координаты по адресу через апи гугл карт
        getCoordinatesFromAddress: function (address) {
            if (!address) {
                return {
                    lat: null,
                    lng: null
                };
            }
            var coordinates = {};
            let url = 'https://maps.googleapis.com/maps/api/geocode/json';
            $.ajax({
                'async': false,
                'type': "GET",
                'global': false,
                'url': url,
                'data': { 'key': widget.config.googleMapsApiKey, 'address': address },
            }).fail(function (data) {
                console.log('ajax fail');
                console.log(data);
            }).success(function (data) {
                coordinates = data;
            });
            return coordinates.results[0].geometry.location;
        },

    
        // взять адрес соискателя и его лимит
        getEmployeeData: function () {
            const fields = widget.config.fields;
            const defaults = widget.defaultFieldsData;
            const limit         = yadroFunctions.getFieldValue(fields['Желаемое расстояние']) || defaults['Желаемое расстояние'];
            const address       = yadroFunctions.getFieldValue(fields['Адрес проживания 1'])  || defaults['Адрес проживания 1'];
            const vacancyWanted = yadroFunctions.getFieldValue(fields['Желаемая вакансия'])   || defaults['Желаемая вакансия'];
            const shopAddress   = yadroFunctions.getFieldValue(fields['Магазин квал'])        || defaults['Магазин квал'];
            const shopName      = yadroFunctions.getFieldValue(fields['Клиент'])              || defaults['Клиент'];
            const city          = yadroFunctions.getFieldValue(fields['ГороД'])               || defaults['ГороД'];

            const employeeData = {
                address,
                limit,
                vacancyWanted,
                shopAddress,
                shopName,
                city,
            }
            return employeeData;
        },


        // взять координаты соискателя и его лимит из соответствующих полей в модальном окне
        getEmployeeDataFromModal: function () {
            let address = $(`input[id="${widget.selectors.modalAddressId}"]`)[0].value;
            let limit = $(`input[id="${widget.selectors.modalLimitId}"]`)[0].value;
            let employeeData = {
                address: address,
                limit: parseInt(limit),
            }
            return employeeData;
        },

    
        // взять координаты соискателя и его лимит из соответствующих полей в модальном окне
        getEmployeeDataWithCoordinatesFromModal: function () {
            let employeeData = widget.getters.getEmployeeDataFromModal();
            let coordinates = widget.getters.getCoordinatesFromAddress(employeeData.address);
            employeeData.latitude = coordinates.lat;
            employeeData.longitude = coordinates.lng;
            return employeeData;
        },

    
        // отправить запрос на получении данных о магазинах
        getShopsData: function (longitude, latitude, limit) {
            // если не получил координаты - то есть в доп полях сделки не заполнена информация
            if (!latitude) {
                return [];
            }
            var shopsData = {};
            let url = widget.config.baseUrl + 'scripts/dbApi.php';
            $.ajax({
                'async': false,
                'type': "GET",
                'global': false,
                'url': url,
                'data': { 'method': 'get_shops', 'limit': limit, 'longitude': longitude, 'latitude': latitude },
            }).fail(function (data) {
                console.log('ajax fail');
                console.log(data);
            }).success(function (data) {
                shopsData = data;
            });
            return shopsData;
        },

    
        // получить только названия вакансий из массива с информацией о вакансии
        getVacanciesNames: function (vacancies) {
            let names = [];
            for (let i = 0; i < vacancies.length; i += 1) {
                names.push(vacancies[i].name);
            }
            return names;
        },


        // посчитать загрузку для массива
        getVacanciesNamesWithNeed: function (vacancies, totalNeed, totalRealNeed) {
            let names = [];
            for (let i = 0; i < vacancies.length; i += 1) {
                const need = (totalNeed / (totalNeed - totalRealNeed)) * 100;
                const fullVacancyStr = `${vacancies[i]} ${parseFloat(need.toFixed(2))}%`;
                names.push(fullVacancyStr);
            }
            return names;
        },
    
        // посчитать загрузку для одной вакансии
        getVacancyNameWithNeed: function (vacancyName, vacancyObj) {
            const x = parseInt(vacancyObj.need);
            const y = parseInt(vacancyObj.real_need);
            let need = 0;
            if (x != 0) {
                need = ((x - y) / x) * 100;
            }

            need = parseFloat(need.toFixed(2));
            const fullVacancyStr = `${vacancyName} ${need}%`;
            const debugStr = `((${x} - ${y}) / ${x}) * 100 = ${need}`;
            return [need, debugStr];
        },


        // сделать из названий вакансий кликабельные подчеркнутые элементы
        getClickableVacancies: function (vacancies, index) {
            const spanStart = `<span class="${widget.selectors.clickableVacancyClass}" id="table-entry-vacancy-name-${index}" data-table-position="${index}" style="text-decoration: underline; cursor: pointer;">`;
            const spanEnd = '</span>';
            let clickableVacancies = [];
            for (let i = 0; i < vacancies.length; i += 1) {
                clickableVacancies.push(spanStart + vacancies[i] + spanEnd);
            }
            return clickableVacancies;
        },
    

        getClickableAddress: function (from, to) {
            from = encodeURIComponent(from);
            toEncoded = encodeURIComponent(to);
            const routeUrl = `https://www.google.com/maps/dir/${from}/${toEncoded}`;
            return `<a target="_blank" href="${routeUrl}">${to}</a>`
        },

    
        // сделать из названий магазина элемент который можно найти по id
        getSearchableShopName: function (shopName, index) {
            return `<span id="table-entry-shop-name-${index}" data-table-position="${index}" >${shopName}</span>`;
        },

    };


    // методы хелперы
    this.helpers = {
        // взять данные из запроса и превратить их в данные для рендера таблицы
        convertDataToTableFormat: function (inputData, employeeAddress, sortFunction=widget.helpers.nearToFarSort) {
            const buttonHtml = (sortFunction != widget.helpers.nearToFarSort) ? widget.html.sortBtnHtmlUp : widget.html.sortBtnHtmlDown;
            // const distanceTitle = 'Расстояние' + '&nbsp' + buttonHtml;
            const distanceTitle = `<span id="${widget.selectors.sortBtnId}" style="cursor: pointer;">Расстояние</span>`;
            let data = {
                not_show_chbx: true,
                fields: [
                    {
                        code: 'shopName',
                        title: 'Название магазина',
                        shown: true,
                        template: 'text_raw',
                        width: 20,
                    },
                    // original
                    {
                        code: 'address',
                        title: 'Адрес',
                        shown: true,
                        template: 'text_raw',
                        width: 40,
                    },
                    // for debug only
                    {
                        code: 'debug',
                        title: 'Подсчет загрузки',
                        shown: false,
                        template: 'text_raw',
                        width: 40,
                    },
                    {
                        code: 'distance',
                        title: distanceTitle,
                        shown: true,
                        template: 'text_raw',
                        width: 15,

                    },
                    {
                        code: 'vacancy',
                        title: 'Вакансия',
                        shown: true,
                        template: 'text_raw',

                    }
                ],
                items: []
            }
            

            for (let i = 0; i < inputData.length; i += 1) {
                const shopNameRaw = inputData[i].shop_name;
                const vacanciesRaw = inputData[i].vacancies;
                const clickableAddress = widget.getters.getClickableAddress(employeeAddress, inputData[i].shop_address);

                const shopName = widget.getters.getSearchableShopName(shopNameRaw, i);
                let vacancies = widget.getters.getVacanciesNames(vacanciesRaw);
                vacancies = widget.getters.getClickableVacancies(vacancies, i);

                for (let j = 0; j < vacanciesRaw.length; j += 1) {

                    const [need, debugStr] = widget.getters.getVacancyNameWithNeed(vacancies[j], vacanciesRaw[j]);

                    if (widget.ignoreFull) {
                        if (need >= 100.0) {
                            continue;
                        }
                    }
                    const tempShopData = {
                        tags: [],
                        index: i,
                        shopNameRaw: shopNameRaw,
                        shopName: shopName,
                        address: clickableAddress,
                        distanceNumber: parseFloat(inputData[i].distance.toFixed(2)),
                        distance: `${inputData[i].distance.toFixed(2)} км`,
                        vacancy: `${vacancies[j]} ${need}%`,
                        needRaw: need,
                        vacancyRaw: vacanciesRaw[j].name,
                        debug: debugStr,
                    };
                    data.items.push(tempShopData);
                }
            }

            data.items = data.items.filter(widget.helpers.substringSearch);
            // data.items = data.items.filter(widget.helpers.ignoreFull);
            data.items.sort(sortFunction);
            return data;
        },
        nearToFarSort: function (a, b) {
            if (a.distanceNumber < b.distanceNumber) {
                return -1;
            }
            if (a.distanceNumber > b.distanceNumber) {
                return 1;
            }
            return 0;
        },
        farToNearSort: function (a, b) {
            if (a.distanceNumber < b.distanceNumber) {
                return 1;
            }
            if (a.distanceNumber > b.distanceNumber) {
                return -1;
            }
            return 0;
        },

        substringSearch: function(element) {
            const queryShops = $(`#${widget.selectors.modalSearchShopsId}`)[0].value;
            const queryVacancies = $(`#${widget.selectors.modalSearchVacanciesId}`)[0].value;
            const pattern = "ё";
            const re = new RegExp(pattern, "g");
            const stringToSearchShops = element.shopNameRaw.toLowerCase().replace(re, "е");
            const stringToSearchVacancies = element.vacancyRaw.toLowerCase().replace(re, "е");

            const resultShops = stringToSearchShops.includes(queryShops.toLowerCase());
            const resultVacancies = stringToSearchVacancies.includes(queryVacancies.toLowerCase());

            const result = (
                resultShops
                && resultVacancies
            );
            return result;
        },


        ignoreFull: function (element) {
            if (element.needRaw >= 100.0) {
                return false;
            }
            return true;
        }
    };


    // методы обработчики событий
    this.handlers = {
        modalLimitOnChange: function (event){
            let inputValue = this.value;
            let onlyLimit = inputValue.replace(/ км/g, '');
            this.value = String(onlyLimit) + ' км';
        },

        modalDestroyHandler: function () {
            // взять данные из модального окна
            const dataFromModal = widget.getters.getEmployeeDataFromModal();
            // взять данные из карточки
            const dataFromCard = widget.getters.getEmployeeData();

            // если в модальном окне не заданы новые данные то выход
            if ((dataFromCard.address == dataFromModal.address) && (dataFromCard.limit == dataFromModal.limit)) {
                return;
            }
            // если задан новый адрес обновить адрес в карточке
            if (dataFromCard.address != dataFromModal.address) {
                const addressSelector = `input[name="CFV[${widget.config.fields['Адрес проживания 1']}]"]`;
                $(addressSelector)[0].value = dataFromModal.address;
                $(addressSelector).trigger('controls:change');
                $(addressSelector).trigger('input');
            }
            // если задан новый лимит обновить лимит в карточке
            if (dataFromCard.limit != dataFromModal.limit) {
                const limitSelector = `input[name="CFV[${widget.config.fields['Желаемое расстояние']}]"]`;
                $(limitSelector)[0].value = dataFromModal.limit;
                $(limitSelector).trigger('controls:change');
                $(limitSelector).trigger('input');
            }
            AMOCRM.data.current_card.save();
        },


        // по клику на кнопку загрузить магазины внутри модалки
        loadShopsBtnOnClick: function () {
            widget.renderers.reRenderTable();
        },



        fastInput3OnClick: function () {
            let inputElement = $(`#${widget.selectors.modalLimitId}`)[0];
            inputElement.value = '3 км';
        },

        fastInput5OnClick: function () {
            let inputElement = $(`#${widget.selectors.modalLimitId}`)[0];
            inputElement.value = '5 км';
        },
        fastInput10OnClick: function () {
            let inputElement = $(`#${widget.selectors.modalLimitId}`)[0];
            inputElement.value = '10 км';
        },



        // по клику на кнопку доступные магазины
        availableShopsBtnOnClick: function () {
            // этот спиннер нужен, иначе не работает
            widget.renderers.renderSpinner('#page_holder');

            // порядок такой:
            // рендерить пустую модалку чтобы туда потом добавлять элементы
            // два верхних поля и кнопку и шапку таблицы можно показать сразу
            // показать спиннер поверх модалки
            // отправить запрос
            // получить данные
            // с данными составить таблицу
            // убрать спиннер
            // перерендериваем таблицу

            // рендерить пустую модалку чтобы туда потом добавлять элементы
            // 1. render empty modal
            widget.renderers.renderEmptyModal();

            let employeeData = widget.getters.getEmployeeData();

            // два верхних поля и кнопку и шапку таблицы можно показать сразу
            // 2. render modal with a form and table top
            widget.renderers.renderForm(`#${widget.selectors.modalFormId}`, employeeData);
            widget.renderers.renderEmptyTable(`#${widget.selectors.modalTableId}`);

            // показать спиннер
            // отправить запрос
            // получить данные
            // с данными составить таблицу
            // убрать спиннер
            // перерендериваем таблицу
            widget.renderers.reRenderTable();

        },


        // по клику на кнопку сортировки
        sortBtnOnClick: function (e) {
            // toggle sort direction
            widget.sortDirection = !widget.sortDirection; 
            // true is ascending
            if (widget.sortDirection) {
                widget.renderers.reRenderTable(sortFunction=widget.helpers.nearToFarSort);
            } else {
                widget.renderers.reRenderTable(sortFunction=widget.helpers.farToNearSort);
            }


            // e.preventDefault();
            // e.stopPropagation();
            // const $target = $(`#${widget.selectors.sortBtnId}`);
            // const $class_anim = 'btn--pointer_up';
            // if ($target.hasClass($class_anim)) {
            //     // нажат 2й раз
            //     // btn has class. remove it
            //     $target.removeClass($class_anim);
            //     // class removed. check
            //     widget.renderers.reRenderTable(sortFunction=widget.helpers.nearToFarSort);
            // } else {
            //     // нажат 1й раз
            //     // btn doesnt have class. add it
            //     $target.addClass($class_anim);
            //     // class added. check
            //     widget.renderers.reRenderTable(sortFunction=widget.helpers.farToNearSort);
            // }
        },


        // по клику на название вакансии в таблице
        vacancyNameOnClick: function (event) {
            event.preventDefault();
            event.stopPropagation();
            
            const $target = $(event.currentTarget);
            const vacancyName = $target[0].innerHTML;
            const tablePosition = $target.attr('data-table-position');
            const shopName = $(`#table-entry-shop-name-${tablePosition}`)[0].innerHTML;

            const shopWantedSelector = `input[name="CFV[${widget.config.fields['Магазин квал']}]"]`;
            const vacancyWantedSelector = `input[name="CFV[${widget.config.fields['Желаемая вакансия']}]"]`;

            $(shopWantedSelector)[0].value = shopName;
            $(vacancyWantedSelector)[0].value = vacancyName;

            $(shopWantedSelector).trigger('controls:change');
            $(shopWantedSelector).trigger('input');

            $(vacancyWantedSelector).trigger('controls:change');
            $(vacancyWantedSelector).trigger('input');

            AMOCRM.data.current_card.save();
            // теперь закрыть окно
            $(`span.${widget.selectors.modalCloseClass}`).trigger('click');
        },

        // при вводе в поле поиска по магазину
        modalSearchShopsOnKeyUp: function (event) {
            if (event.key === 'Enter' || event.keyCode === 13) {
                widget.renderers.reRenderTable();
            }
        },


        // при вводе в поле поиска по магазину
        modalSearchVacanciesOnKeyUp: function (event) {
            if (event.key === 'Enter' || event.keyCode === 13) {
                widget.renderers.reRenderTable();
            }
        },

        // по клику на кнопку искать магазины внутри модалки
        searchBtnOnClick: function () {
            widget.handlers.modalSearchVacanciesOnKeyUp({key: 'Enter'});
        },


        ignoreFullCheckboxOnChange: function (element) {
            // console.log('ignoreFullCheckboxOnChange');
            // console.log('toggle widget.ignoreFull. it is now');
            widget.ignoreFull = !widget.ignoreFull;
            // console.log(widget.ignoreFull);
        }


    };


    // методы для рендера
    this.renderers = {
        // рендер голубой кнопки амо
        renderBlueButton: function (selector, id, class_name = '', text = 'button', isAppend = false) {
            let full_class_name = `button-input_blue ${class_name}`;

            if (isAppend) {
                yadroFunctions.render("/tmpl/controls/button.twig", {
                    id,
                    class_name: full_class_name,
                    text
                }, (html) => {
                    $(selector).append(html);
                });
            } else {
                yadroFunctions.render("/tmpl/controls/button.twig", {
                    id,
                    class_name: full_class_name,
                    text
                }, (html) => {
                    $(selector).after(html);
                });
            }
        },

        // рендер серой кнопки амо
        renderGreyButton: function (selector, id, class_name = '', text = 'button', isAppend = false) {
            let full_class_name = `button-input-more ${class_name}`;

            if (isAppend) {
                yadroFunctions.render("/tmpl/controls/button.twig", {
                    id,
                    class_name: full_class_name,
                    text
                }, (html) => {
                    $(selector).append(html);
                });
            } else {
                yadroFunctions.render("/tmpl/controls/button.twig", {
                    id,
                    class_name: full_class_name,
                    text
                }, (html) => {
                    $(selector).after(html);
                });
            }
        },

        // рендер кнопки доступные магазины
        renderAvailableShopsBtn: function () {
            let selector = widget.selectors.availableShopsBtnAfter;
            let id = widget.selectors.availableShopsBtnId;
            let class_name = '';
            let text = 'Доступные магазины';
            widget.renderers.renderBlueButton(
                selector,
                id,
                class_name,
                text
            );
        },


        // рендер модального окна с полученным html
        renderModal: function (id, innerHTML) {
            const html = '' +
                `<div class="widget_settings_block intr-widget_settings_block">\
                    <div id="${id}" class="widget_settings_block__fields" >\
                    ${innerHTML}\
                    </div>\
                </div>`;

                new Modal({
                class_name: `modal-window ${widget.selectors.modalClass}`,
                // style: 'overflow: auto',
                init: function ($modal_body) {
                    $modal_body
                    .append(`<span class="modal-body__close"><span class="icon ${widget.selectors.modalCloseClass}"></span></span>`)
                    .append(html)
                    .trigger('modal:loaded')
                    .trigger('modal:centrify')
                    
                    if (typeof callback == 'function') {
                        callback($('.' + contentClass), $modal_body);
                    }
                },
                destroy: function () {
                    widget.handlers.modalDestroyHandler();
                    if (typeof destroy == 'function') {
                        destroy();
                    }
                }
            });
        },


        // рендер пустого модального окна
        renderEmptyModal: function () {
            const html = '' +
                `<div id="${widget.selectors.modalFormId}"></div>\
                <br>\
                <div id="${widget.selectors.modalTableId}" style="overflow: auto; height:530px;"></div>`;

            widget.renderers.renderModal(widget.selectors.modalId, html);
        },

        // рендер формы в модальном окне
        renderForm: function (selector, employeeData = {}) {
            const space = '&nbsp';

            // поле ввода адреса
            let data = {
                name: 'address',
                placeholder: 'Адрес соискателя',
                value: (employeeData) ? employeeData.address : null,
                type: 'text',
                id: widget.selectors.modalAddressId,
            };
            yadroFunctions.render('/tmpl/controls/input.twig', data, html => {
                $(selector).append(space + space + space + html + space)
            });

            // поле ввода лимита расстояния
            data = {
                name: 'limit',
                placeholder: 'Максимальное расстояние в км',
                value: (employeeData) ? String(employeeData.limit) + ' км' : '0 км',
                // type: 'number',
                type: 'text',
                id: widget.selectors.modalLimitId,
            };
            yadroFunctions.render('/tmpl/controls/input.twig', data, html => {
                $(selector).append(html + space)
            });



            // рендер кнопок для быстрого воода расстояния
            widget.renderers.renderFastLimitInputButtons();

            // рендер кнопки загрузить магазины
            widget.renderers.renderLoadShopsButton();


            // поле ввода фильтр по магазинам
            data = {
                name: 'search',
                placeholder: 'Фильтр по магазинам',
                type: 'text',
                id: widget.selectors.modalSearchShopsId,
            };
            yadroFunctions.render('/tmpl/controls/input.twig', data, html => {
                $(selector).append('<br>' + space + space + space + html + space)
            });
            
            // поле ввода фильтр по вакансиям
            data = {
                name: 'search-vacancies',
                placeholder: 'Фильтр по вакансиям',
                type: 'text',
                id: widget.selectors.modalSearchVacanciesId,
            };
            yadroFunctions.render('/tmpl/controls/input.twig', data, html => {
                $(selector).append(html + space)
            });

            // рендер кнопки поиск
            widget.renderers.renderSearchButton();




            widget.renderers.renderIgnoreFullVacanciesCheckbox(selector);
        },


        // рендер таблицы по данным в табличном формате
        renderTable: function (selector, data) {
            yadroFunctions.render('/tmpl/list/inner.twig', data, html => {
                $(selector).append(html)
            });
        },


        // рендер пустой таблицы
        renderEmptyTable: function (selector) {
            widget.renderers.renderTable(selector, []);
        },


        // перерендерить таблицу
        reRenderTable: function (sortFunction=widget.helpers.nearToFarSort) {
            // порядок такой:
            // показать спиннер
            // взять данные соискателя
            // отправить запрос
            // получить данные
            // с данными составить таблицу
            // убрать старую таблицу
            // убрать спиннер
            // перерендериваем таблицу

            // показать спиннер
            widget.renderers.renderSpinner(`#${widget.selectors.modalId}`);

            // без таймаута не работает
            setTimeout(function () {

                // взять данные соискателя
                const employeeData = widget.getters.getEmployeeDataWithCoordinatesFromModal();


                // отправить запрос
                // получить данные
                const shopsData = widget.getters.getShopsData(employeeData.longitude, employeeData.latitude, employeeData.limit);


                // с данными составить таблицу
                const tableData = widget.helpers.convertDataToTableFormat(shopsData, employeeData.address, sortFunction=sortFunction);


                // убрать старую таблицу
                widget.renderers.removeTable(`#${widget.selectors.modalTableId}`);


                // перерендериваем таблицу
                widget.renderers.renderTable(`#${widget.selectors.modalTableId}`, tableData);

                setTimeout(function () {
                    // убрать спиннер
                    widget.renderers.removeSpinner(`#${widget.selectors.modalId}`);
                }, 1);

            }, 1);

            // убрать спиннер
            // нужно сделать это второй раз, иначе не работает
            widget.renderers.removeSpinner(`#${widget.selectors.modalId}`);
        },


        // рендер кнопки для поиска 
        renderFastLimitInputButtons: function () {
            widget.renderers.renderGreyButton(
                selector    = widget.selectors.loadShopsBtnAppend,
                id          = widget.selectors.limitFastInput['3'],
                class_name  = '',
                text        = '3 км',
                isAppend    = true
            );

            widget.renderers.renderGreyButton(
                selector    = widget.selectors.loadShopsBtnAppend,
                id          = widget.selectors.limitFastInput['5'],
                class_name  = '',
                text        = '5 км',
                isAppend    = true
            );


            widget.renderers.renderGreyButton(
                selector    = widget.selectors.loadShopsBtnAppend,
                id          = widget.selectors.limitFastInput['10'],
                class_name  = '',
                text        = '10 км',
                isAppend    = true
            );
        },


        // рендер кнопки для загрузки магазинов
        renderLoadShopsButton: function () {
            widget.renderers.renderBlueButton(
                selector    = widget.selectors.loadShopsBtnAppend,
                id          = widget.selectors.loadShopsBtnId,
                class_name  = 'button-input_blue',
                text        = 'Загрузить магазины',
                isAppend    = true
            );
        },


        // рендер кнопки для поиска 
        renderSearchButton: function () {
            widget.renderers.renderGreyButton(
                selector    = widget.selectors.loadShopsBtnAppend,
                id          = widget.selectors.searchBtnId,
                class_name  = '',
                text        = 'Поиск',
                isAppend    = true
            );
        },



        renderIgnoreFullVacanciesCheckbox: function (selector) {
            const space = '&nbsp';
            let id = widget.selectors.ignoreFullCheckboxId;
            data = {
                name: 'ignoreFullVacanciesCheckbox',
                text: 'Игнорировать вакансии с загрузкой 100%',
                checked: widget.ignoreFull,
                id: id,
            };
            yadroFunctions.render('/tmpl/controls/checkbox.twig', data, html => {
                $(selector).append('<br>' + space + space + space + html)
            });
            // yadroFunctions.render('/tmpl/controls/checkbox.twig', data, html => {
            //     $(selector).append(html)
            // });
        },


        // рендер спиннера body
        renderSpinner: function (selector = 'body') {
            $(selector).append('<div class="default-overlay list__overlay default-overlay-visible" id="page_change_loader"><span class="spinner-icon spinner-icon-abs-center"></span></div>');
        },


        // убрать спиннер body
        removeSpinner: function (selector = 'body') {
            $('#page_change_loader').remove();
        },


        // убрать таблицу
        removeTable: function (selector) {
            $(selector)[0].innerHTML = '';
        },
    };


    // методы по умолчанию
    // вызывается один раз при инициализации виджета, в этой функции мы вешаем события на $(document)
    this.bind_actions = function () {
        $(document).on('click', `#${widget.selectors.availableShopsBtnId}`,     this.handlers.availableShopsBtnOnClick);
        $(document).on('click', `#${widget.selectors.loadShopsBtnId}`,          this.handlers.loadShopsBtnOnClick);
        $(document).on('click', `#${widget.selectors.limitFastInput['3']}`,     this.handlers.fastInput3OnClick);
        $(document).on('click', `#${widget.selectors.limitFastInput['5']}`,     this.handlers.fastInput5OnClick);
        $(document).on('click', `#${widget.selectors.limitFastInput['10']}`,    this.handlers.fastInput10OnClick);
        $(document).on('click', `.${widget.selectors.clickableVacancyClass}`,   this.handlers.vacancyNameOnClick);
        $(document).on('click', `#${widget.selectors.sortBtnId}`,               this.handlers.sortBtnOnClick);
        $(document).on('click', `#${widget.selectors.searchBtnId}`,             this.handlers.searchBtnOnClick);

        $(document).on('keyup', `#${widget.selectors.modalSearchShopsId}`,      this.handlers.modalSearchShopsOnKeyUp);
        $(document).on('keyup', `#${widget.selectors.modalSearchVacanciesId}`,  this.handlers.modalSearchVacanciesOnKeyUp);

        $(document).on('change', `#${widget.selectors.modalLimitId}`,           this.handlers.modalLimitOnChange);

        $(document).on('change', `#${widget.selectors.ignoreFullCheckboxId}`,   this.handlers.ignoreFullCheckboxOnChange);

    };


    // вызывается каждый раз при переходе на страницу
    this.render = function () {
        this.renderers.renderAvailableShopsBtn();
    };


    // вызывается один раз при инициализации виджета, в этой функции мы загружаем нужные данные, стили и.т.п
    this.init = function () {
        console.log('====init of gigantSearchNearestShops====');
    };


    // метод загрузчик, не изменяется
    this.bootstrap = function (code) {
        widget.code = code;
        // если frontend_status не задан, то считаем что виджет выключен
        // var status = yadroFunctions.getSettings(code).frontend_status;
        var status = 1;

        if (status) {
            widget.init();
            widget.render();
            widget.bind_actions();
            $(document).on('widgets:load', function () {
                widget.render();
            });
        }
    };
}

yadroWidget.widgets['gigant-search-nearest-shops-dev-0.0'] = new gigantSearchNearestShops();
yadroWidget.widgets['gigant-search-nearest-shops-dev-0.0'].bootstrap('gigant-search-nearest-shops-dev-0.0');