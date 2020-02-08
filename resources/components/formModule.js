Vue.component('formFieldTitle', {
    template: '#formFieldTitle-template',
    props: ['tab-parent', 'field-ref', 'field-ref-index'],
    data: function () {
        return {
            fieldPlaceholder: '',
            fieldID: this.makeid(10),
            fieldName: '',
            fielApiID: ''
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
});

var formModule = new Vue({
    el: '#formModule',
    data: {
        debug: true,
        tabs: [
        ],
        formsElements: [
            {
                name: 'title',
                displayName: 'Titulo',
                icon: 'format_color_text',
                component: 'formFieldTitle'
            },
            {
                name: 'formatText',
                displayName: 'Texto con formato',
                icon: 'format_size'
            },
            {
                name: 'image',
                displayName: 'Imagen',
                icon: 'image'
            },
            {
                name: 'link',
                displayName: 'Link',
                icon: 'insert_link'
            },
            {
                name: 'date',
                displayName: 'Fecha',
                icon: 'date_range'
            },
            {
                name: 'timestamp',
                displayName: 'Fecha y Hora',
                icon: 'access_time'
            },
            {
                name: 'number',
                displayName: 'Numero',
                icon: 'looks_one'
            },
            {
                name: 'dropdown_select',
                displayName: 'Select',
                icon: 'list'
            },
            {
                name: 'bolean',
                displayName: 'Bolean',
                icon: 'check_circle'
            }
        ],
        form_name: 'Nuevo Formulario'
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
            //console.log(data);
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
            this.debug ? console.log('saveData trigger') : null;

            this.getfieldsData();
            let data = {
                form_name : this.form_name,
                tabs : {}
            };
            this.tabs.forEach(element => {
                data.tabs[element.name] = {
                    data: JSON.parse(JSON.stringify(element.fields))
                }
            });
            console.log(data);
            $.ajax({
                type: "POST",
                url: BASEURL + "admin/formularios/saveForm",
                data: data,
                dataType: "json",
                success: function (response) {
                    console.log(response);
                }
            });
        }
    },
    mounted: function () {
        this.$nextTick(function () {
            this.debug ? console.log('formModule mounted') : null;
            this.tabs.push(
                this.getInitialTab()
            );
            this.tabs[0].edited = false;
            $(function () {
                $("#simple-list").dragsort();
            });
        });
    }
});
