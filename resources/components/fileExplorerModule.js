var fileExplorerModule = new Vue({
    el: '#root',
    data: {
        loader: false,
        files: [],
        recentlyFiles: []
    },
    computed: {
        getFolders() {
            return this.files.filter(fileobject => {
                return (fileobject.file_type == 'folder');
            })
        },
        getFiles() {
            return this.files.filter(fileobject => {
                return (fileobject.file_type != 'folder');
            })
        }
    },
    methods: {
        getIcon(fileObject) {
            let icon = 'description';
            switch (fileObject.file_type) {
                case 'folder':
                    icon = 'folder';
                    break;
                case 'img':
                case 'png':
                case 'gif':
                    icon = 'insert_photo';
                    break;
                case 'html':
                    icon = 'web';
                    break;

                default:
                    break;
            }
            return icon;
        },
        getExtention(fileObject) {
            if (fileObject.file_type == 'folder') {
                return ''
            } else {
                return '.' + fileObject.file_type;
            }
        }
    },
    filters: {
        shortName: function (value) {
            if (!value) return ''
            value = value.toString()
            if (value.length > 15) {
                return value.substr(0, 15) + '...'
            } else {
                return value.substr(0, 15)
            }
        }
    },
    mounted: function () {
        this.$nextTick(function () {
            var self = this;
            $.ajax({
                type: "POST",
                url: BASEURL + "admin/archivos/ajax_get_files",
                data: {
                    'path': './'
                },
                dataType: "json",
                success: function (response) {
                    if (response.code == 200) {
                        self.files = response.data;
                    }
                }
            });
        });
    }
});