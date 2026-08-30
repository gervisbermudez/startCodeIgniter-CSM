<script>
window.SITEFORMS_I18N = {
    name: {!! json_encode(lang('siteforms_name')) !!},
    template: {!! json_encode(lang('siteforms_template')) !!},
    submissions: {!! json_encode(lang('siteforms_submissions')) !!},
    created: {!! json_encode(lang('siteforms_created')) !!},
    status: {!! json_encode(lang('siteforms_status')) !!},
    options: {!! json_encode(lang('siteforms_options')) !!},
    preview: {!! json_encode(lang('siteforms_preview')) !!},
    form: {!! json_encode(lang('siteforms_form')) !!},
    active: {!! json_encode(lang('siteforms_active')) !!},
    inactive: {!! json_encode(lang('siteforms_inactive')) !!},
    statusNew: {!! json_encode(lang('siteforms_status_new')) !!},
    statusSeen: {!! json_encode(lang('siteforms_status_seen')) !!},
    edit: {!! json_encode(lang('edit')) !!},
    delete: {!! json_encode(lang('delete')) !!},
    details: {!! json_encode(lang('siteforms_details')) !!},
    copy: {!! json_encode(lang('siteforms_copy')) !!},
    copied: {!! json_encode(lang('siteforms_copied')) !!},
    markSeen: {!! json_encode(lang('siteforms_mark_seen')) !!},
    viewSubmissions: {!! json_encode(lang('siteforms_view_submissions')) !!},
    exportCsv: {!! json_encode(lang('siteforms_export')) !!},
    exportEmpty: {!! json_encode(lang('siteforms_export_empty')) !!},
    copySnippet: {!! json_encode(lang('siteforms_copy_snippet')) !!},
    snippetCopied: {!! json_encode(lang('siteforms_snippet_copied')) !!},
    saved: {!! json_encode(lang('siteforms_saved')) !!},
    error: {!! json_encode(lang('siteforms_error')) !!},
    toast_saved: {!! json_encode(lang('siteforms_saved')) !!},
    toast_error: {!! json_encode(lang('siteforms_error')) !!},
    toast_deleted: {!! json_encode(lang('siteforms_deleted')) !!},
    empty: {!! json_encode(lang('siteforms_empty')) !!},
    emptyCta: {!! json_encode(lang('siteforms_empty_cta')) !!},
    inboxEmpty: {!! json_encode(lang('siteforms_inbox_empty')) !!},
    confirmDelete: {!! json_encode(lang('confirm_delete')) !!},
    confirmDeleteBody: {!! json_encode(lang('siteforms_confirm_delete')) !!},
    newTooltip: {!! json_encode(lang('siteforms_new_tooltip')) !!}
};
window.ADMIN_LANG = Object.assign({}, window.ADMIN_LANG || {}, {
    toast_saved: window.SITEFORMS_I18N.toast_saved,
    toast_error: window.SITEFORMS_I18N.toast_error,
    toast_deleted: window.SITEFORMS_I18N.toast_deleted
});
if (typeof window.lang !== 'function') {
    window.lang = function (key) {
        if (window.SITEFORMS_I18N && window.SITEFORMS_I18N[key]) {
            return window.SITEFORMS_I18N[key];
        }
        return key;
    };
}
</script>
