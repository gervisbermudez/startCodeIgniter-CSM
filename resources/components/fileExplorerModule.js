var fileExplorerModule = new Vue({
    el: '#root',
    data: {
        root: './',
        curDir: '',
        loader: false,
        files: [],
        recentlyFiles: [],
        backto: null,
        search: ''
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

                let path = tempArray.join('/');
                if (path) {
                    return {
                        path: this.root + path + '/',
                        folder: element
                    }
                } else {
                    return {
                        path: this.root + path,
                        folder: element
                    }
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
                case 'jpg':
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
                        setTimeout(() => {
                            var elems = document.querySelectorAll('.dropdown-trigger');
                            var instances = M.Dropdown.init(elems, {});
                        }, 2000);
                    }
                }
            });
        },
        filterFiles(filter) {
            switch (filter) {
                case 'important':
                    this.getFilterFiles('important', ['1']);
                    break;
                case 'trash':
                    this.getFilterFiles('status', ['0']);
                    break;
                case 'images':
                    this.getFilterFiles('file_type', ['jpg', 'png', 'gif']);
                    break;
                case 'doc':
                    this.getFilterFiles('file_type', ['pdf', 'doc']);
                    break;
                case 'docs':
                    this.getFilterFiles('file_type', ['pdf', 'doc', 'xls']);
                    break
                case 'audio':
                    this.getFilterFiles('file_type', ['acc, mp3']);
                    break
                case 'video':
                    this.getFilterFiles('file_type', ['mp4']);
                    break
                case 'zip':
                    this.getFilterFiles('file_type', ['zip', 'rar']);
                    break
                default:
                    break;
            }
        },
        resetSearch() {
            this.search = null;
            this.navigateFiles(this.root);
        },
        searchfiles() {
            if (this.search) {
                this.getFilterFiles('file_name', [this.search]);
            } else {
                this.navigateFiles(this.root);
            }
        },

        getFilterFiles(filter_name, filter_value) {
            var self = this;
            $.ajax({
                type: "POST",
                url: BASEURL + "admin/archivos/ajax_get_filter_files",
                data: {
                    'filter_name': filter_name,
                    'filter_value': filter_value
                },
                dataType: "json",
                success: function (response) {
                    if (response.code == 200) {
                        self.files = response.data;
                        setTimeout(() => {
                            var elems = document.querySelectorAll('.dropdown-trigger');
                            var instances = M.Dropdown.init(elems, {});
                        }, 2000);
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