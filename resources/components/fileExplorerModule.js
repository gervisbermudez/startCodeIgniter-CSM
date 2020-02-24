var fileExplorerModule = new Vue({
    el: '#root',
    data: {
        root: './',
        curDir: '',
        loader: false,
        files: [],
        recentlyFiles: [],
        backto: null,
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
        },
        getBackPath() {
            if (this.getbreadcrumb.length > 0) {
                return this.getbreadcrumb[this.getbreadcrumb.length - 1].path;
            } else {
                return this.root;
            }
        },
        getbreadcrumb() {
            let breadcrumb = this.curDir.split('/').filter(value => {
                return (value != '' && value != '.');
            });
            breadcrumb = breadcrumb.map((element, index) => {
                let tempArray = [];
                for (let i = 0; i < index; i++) {
                    tempArray.push(breadcrumb[i]);
                }
                return {
                    path: this.root + tempArray.join('/'),
                    folder: element
                }
            });
            return breadcrumb;
        }
    },
    methods: {
        getIcon(fileObject) {
            let icon = 'far fa-file';
            switch (fileObject.file_type) {
                case 'folder':
                    icon = 'far fa-folder';
                    break;
                case 'img':
                case 'png':
                case 'gif':
                    icon = 'fas fa-file-image';
                    break;
                case 'html':
                    icon = 'fab fa-html5';
                    break;
                case 'css':
                case 'min.css':
                    icon = 'fab fa-css3-alt';
                    break
                case 'txt':
                    icon = 'far fa-file-alt';
                    break;
                case 'php':
                case 'blade.php':
                    icon = 'fab fa-php';
                    break;
                case 'js':
                case 'json':
                case 'min.js':
                    icon = 'fab fa-js';
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
        },
        navigateFiles(path) {
            var self = this;
            self.backto = self.getBackPath;
            self.curDir = path;
            if (path == self.root) {
                self.backto = null;
            }

            $.ajax({
                type: "POST",
                url: BASEURL + "admin/archivos/ajax_get_files",
                data: {
                    'path': path
                },
                dataType: "json",
                success: function (response) {
                    if (response.code == 200 && response.data.length) {
                        self.files = response.data;
                    }
                }
            });
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
            this.navigateFiles(self.root);
        });
    }
});