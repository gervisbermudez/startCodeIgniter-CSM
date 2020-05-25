Vue.component('formFieldTitle', {
    template: '#formFieldTitle-template',
    props: ['tab-parent', 'field-ref', 'field-ref-index', 'serve-data'],
    data: function () {
        return {
            fieldPlaceholder: '',
            fieldID: this.makeid(10),
            fieldName: '',
            fielApiID: '',
        }
    },
    methods: {
        convertfielApiID() {
            this.fielApiID = this.fieldName.toLowerCase().replace(/ /g, '_').replace(/[^\w-]+/g, '');
            this.fieldPlaceholder = this.fieldName.toLowerCase();
        },
        makeid(length) {
            var result = '';
            var characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
            var charactersLength = characters.length;
            for (var i = 0; i < length; i++) {
                result += characters.charAt(Math.floor(Math.random() * charactersLength));
            }
            return result;
        }
    },
    mounted: function () {
        this.$nextTick(function () {
            for (const key in this.serveData) {
                if (this.serveData.hasOwnProperty(key)) {
                    const element = this.serveData[key];
                    this[key] = element;
                }
            }
        });
    },
});

var formModule = new Vue({
    el: '#formModule',
    data: {
        debug: DEBUGMODE,
        editMode: false,
        loader: true,
        tabs: [
        ],
        formsElements: [
            {
                name: 'title',
                displayName: 'Titulo',
                icon: 'format_color_text',
                component: 'formFieldTitle',
                data: {
                    hola: 'mundo'
                }
            },
            {
                name: 'formatText',
                displayName: 'Texto con formato',
                icon: 'format_size',
                data: {
                    hola: 'mundo'
                }
            },
            {
                name: 'image',
                displayName: 'Imagen',
                icon: 'image',
                data: {
                    hola: 'mundo'
                }
            },
            {
                name: 'link',
                displayName: 'Link',
                icon: 'insert_link',
                data: {
                    hola: 'mundo'
                }
            },
            {
                name: 'date',
                displayName: 'Fecha',
                icon: 'date_range',
                data: {
                    hola: 'mundo'
                }
            },
            {
                name: 'timestamp',
                displayName: 'Fecha y Hora',
                icon: 'access_time',
                data: {
                    hola: 'mundo'
                }
            },
            {
                name: 'number',
                displayName: 'Numero',
                icon: 'looks_one',
                data: {
                    hola: 'mundo'
                }
            },
            {
                name: 'dropdown_select',
                displayName: 'Select',
                icon: 'list',
                data: {
                    hola: 'mundo'
                }
            },
            {
                name: 'bolean',
                displayName: 'Bolean',
                icon: 'check_circle',
                data: {
                    hola: 'mundo'
                }
            }
        ],
        form_name: 'Nuevo Formulario',
        form_status: true
    },
    methods: {
        getInitialTab() {
            return {
                name: 'Tab ' + (this.tabs.length + 1),
                tabID: this.makeid(10),
                edited: true,
                active: true,
                fields: []
            }
        },
        setActive(index) {
            this.tabs.map(el => {
                el.active = false;
                return el;
            });
            this.tabs[index].active = true;
        },
        addTab() {
            this.debug ? console.log('addTab trigger') : null;
            this.tabs.push(
                this.getInitialTab()
            );
            this.setActive(this.tabs.length - 1);
        },
        saveTab(tab) {
            this.debug ? console.log('saveTab trigger') : null;
            this.tabs[tab].edited = false;

        },
        makeid(length) {
            var result = '';
            var characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
            var charactersLength = characters.length;
            for (var i = 0; i < length; i++) {
                result += characters.charAt(Math.floor(Math.random() * charactersLength));
            }
            return result;
        },
        deleteTab(index) {
            this.debug ? console.log('deleteTab trigger') : null;
            if (this.tabs.length == 1) {
                return false;
            }
            this.tabs.splice(index, 1);
        },
        getActiveTab() {
            let activeTab;
            this.tabs.forEach((element, index) => {
                if (element.active) {
                    activeTab = index;
                }
            });
            return activeTab;
        },
        addField(formField) {
            this.debug ? console.log('addField trigger') : null;

            this.tabs[this.getActiveTab()].fields.push(JSON.parse(JSON.stringify(formField)));

            setTimeout(() => {
                var elems = document.querySelectorAll('.collapsible');
                M.Collapsible.init(elems, {});
            }, 2000);
        },
        getfieldsData() {
            this.debug ? console.log('getfieldsData trigger') : null;
            fieldsComponents = formModule.$refs;
            for (const key in fieldsComponents) {
                if (fieldsComponents.hasOwnProperty(key)) {
                    const element = fieldsComponents[key];
                    for (let index = 0; index < element.length; index++) {
                        const component = element[index];
                        formModule.setFieldData(component.tabParent.tabID, component.fieldRefIndex, JSON.parse(JSON.stringify(component.$data)));
                    }
                }
            }
        },
        setFieldData(tabID, fieldIndex, data) {
            this.debug ? console.log('setFieldData trigger') : null;
            this.tabs.map(element => {
                if (element.tabID == tabID) {
                    element.fields[fieldIndex].data = data;
                }
            });
        },
        saveData() {
            $('html, body').animate({ scrollTop: 0 }, 600);
            this.loader = true;
            this.debug ? console.log('saveData trigger') : null;
            this.getfieldsData();
            let data = {
                form_name: this.form_name,
                form_status: this.form_status ? 1 : 0,
                tabs: {}
            };
            this.tabs.forEach(element => {
                if (element.fields.length < 0) {
                    return false;
                }
                data.tabs[element.name] = {
                    tab_name: element.name,
                    fields: element.fields
                }
            });
            if (this.editMode) {
                data.form_id = form_id;
                var url = BASEURL + "admin/formularios/updateForm";
                $.ajax({
                    type: "POST",
                    url: url,
                    data: {
                        data: JSON.stringify(data)
                    },
                    dataType: "json",
                    success: function (response) {
                        self.debug ? console.log(url, response) : null;
                        if (response.data) {
                            window.location = BASEURL + 'admin/formularios/';
                        }
                    },
                    error: function (response) {
                        self.loader = false;
                        M.toast({ html: 'Ha ocurrido un error' });
                    }
                });
            } else {
                var url = BASEURL + "admin/formularios/saveForm"
                $.ajax({
                    type: "POST",
                    url: url,
                    data: {
                        data: JSON.stringify(data)
                    },
                    dataType: "json",
                    success: function (response) {
                        self.debug ? console.log(url, response) : null;
                        if (response.data) {
                            window.location = BASEURL + 'admin/formularios/';
                        }
                    },
                    error: function (response) {
                        self.loader = false;
                        M.toast({ html: response.responseJSON.error_message });
                      }
                });
            }
        },
        checkEditMode() {
            if (typeof form_id != 'undefined') {
                //cargar datos del formulario
                var self = this;
                self.editMode = true;
                self.form_id = form_id;
                console.log('editMode');
                $.ajax({
                    type: "GET",
                    url: "/admin/formularios/get_form_info/" + form_id,
                    data: {},
                    dataType: "json",
                    success: function (response) {
                        console.log(response);
                        self.updateFormData(response.data)
                    }
                });
            } else {
                this.loader = false;
            }
        },
        updateFormData(data) {
            this.form_name = data[0].form_name;
            this.form_status = (data[0].status == '1');
            this.loader = false;
            this.tabs = [];
            data.forEach(element => {
                let tab = this.getInitialTab();
                tab.edited = false;
                tab.active = false;
                tab.name = element.tab_name
                console.log(element.fields_data);
                let tempField = {};
                element.fields_data.forEach((field) => {
                    tempField = {
                        component: field.component,
                        displayName: field.displayName,
                        icon: field.icon,
                        name: field.field_name,
                        serveData: field.dataconfigs
                    }
                    tab.fields.push(tempField);
                });

                this.tabs.push(tab);
            });
            this.tabs[0].active = true;
            setTimeout(() => {
                var elems = document.querySelectorAll('.collapsible');
                M.Collapsible.init(elems, {});
            }, 2000);
        }
    },
    mounted: function () {
        this.$nextTick(function () {
            this.debug ? console.log('formModule mounted') : null;
            this.tabs.push(
                this.getInitialTab()
            );
            this.tabs[0].edited = false;
            this.checkEditMode();
        });
    }
});
