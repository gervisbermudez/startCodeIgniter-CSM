var formContent = new Vue({
    el: '#root',
    data: {
        content: [],
        loader: true,
        filter: null,
        forms_types: []
    },
    computed: {
        filterContent: function () {
            var self = this;
            if (self.filter) {
                return self.content.filter(function (value) {
                    return value.form_name.toLowerCase().indexOf(self.filter.toLowerCase()) !== -1;
                });
            } else {
                return self.content;
            }
        },
        filterFormsTypes() {
            return this.forms_types.slice(0, 5);
        }
    },
    methods: {
        getDataFromServe() {
            var self = this;
            $.ajax({
                type: "GET",
                url: BASEURL + "admin/formularios/ajax_get_forms_data",
                data: {},
                dataType: "json",
                success: function (response) {
                    console.log(response);
                    if (response.code == 200) {
                        self.content = response.data;
                    }
                }
            });

            $.ajax({
                type: "GET",
                url: BASEURL + "admin/formularios/ajax_get_forms_types",
                data: {},
                dataType: "json",
                success: function (response) {
                    console.log(response);
                    if (response.code == 200) {
                        self.forms_types = response.data;
                        setTimeout(() => {
                            M.AutoInit();
                        }, 300);
                    }
                }
            });
        },
        getFormsTypeUrl(formObject) {
            return BASEURL + 'admin/formularios/addData/' + formObject.id;
        },
        getEditData(itemData) {
            return BASEURL + 'admin/formularios/editData/' + itemData.form_custom_id + '/' + itemData.form_content_id
        }

    },
    mounted: function () {
        this.$nextTick(function () {
            this.getDataFromServe();
        });
    }
});