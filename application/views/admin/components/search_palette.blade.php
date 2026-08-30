<script>
window.SEARCH_I18N = {
    search: {!! json_encode(lang('search')) !!},
    placeholder: {!! json_encode(lang('search_palette_placeholder')) !!},
    hint: {!! json_encode(lang('search_palette_hint')) !!},
    shortcut: {!! json_encode(lang('search_shortcut_hint')) !!},
    viewAll: {!! json_encode(lang('search_view_all')) !!},
    noQuery: {!! json_encode(lang('search_no_query')) !!},
    noResults: {!! json_encode(lang('search_no_results')) !!},
    emptyCta: {!! json_encode(lang('search_empty_cta')) !!},
    minChars: {!! json_encode(lang('search_min_chars')) !!},
    error: {!! json_encode(lang('search_error')) !!},
    type_all: {!! json_encode(lang('search_type_all')) !!},
    type_pages: {!! json_encode(lang('search_type_pages')) !!},
    type_users: {!! json_encode(lang('search_type_users')) !!},
    type_files: {!! json_encode(lang('search_type_files')) !!},
    type_albums: {!! json_encode(lang('search_type_albums')) !!},
    type_categories: {!! json_encode(lang('search_type_categories')) !!},
    type_models: {!! json_encode(lang('search_type_models')) !!},
    type_contents: {!! json_encode(lang('search_type_contents')) !!},
    type_siteforms: {!! json_encode(lang('search_type_siteforms')) !!},
    type_submissions: {!! json_encode(lang('search_type_submissions')) !!},
    type_menus: {!! json_encode(lang('search_type_menus')) !!},
    resultsCount: {!! json_encode(lang('search_results_count')) !!},
    status_published: {!! json_encode(lang('search_status_published')) !!},
    status_draft: {!! json_encode(lang('search_status_draft')) !!},
    status_archived: {!! json_encode(lang('search_status_archived')) !!},
    status_deleted: {!! json_encode(lang('search_status_deleted')) !!}
};
</script>
<div
    id="search-palette"
    class="search-palette-shell"
    :class="{ 'is-open': open }"
    :aria-hidden="open ? 'false' : 'true'"
    @keydown="onShellKeydown"
>
    <div class="search-palette-backdrop" @click="closePalette"></div>
    <div
        class="search-palette z-depth-2"
        role="dialog"
        aria-modal="true"
        :aria-label="i18n.search"
        @click.stop
    >
        <div class="search-palette__header">
            <i class="material-icons prefix" aria-hidden="true">search</i>
            <input
                ref="queryInput"
                id="search-palette-input"
                type="text"
                class="search-palette__input browser-default"
                v-model="query"
                :placeholder="i18n.placeholder"
                :aria-label="i18n.search"
                autocomplete="off"
                spellcheck="false"
                role="combobox"
                aria-autocomplete="list"
                aria-controls="search-palette-list"
                :aria-expanded="open ? 'true' : 'false'"
                :aria-activedescendant="activeDescendant"
            >
            <button
                type="button"
                class="search-palette__close"
                @click="closePalette"
                :aria-label="i18n.hint"
            >
                <i class="material-icons">close</i>
            </button>
        </div>
        <div class="search-palette__chips" v-if="chipTypes.length > 1">
            <button
                type="button"
                class="status-chip"
                v-for="chip in chipTypes"
                :key="chip.id"
                :class="{ active: typeFilter === chip.id }"
                @click="setTypeFilter(chip.id)"
            >
                @{{ chip.label }}
                <span class="search-palette__chip-count" v-if="chip.id !== 'all'">@{{ chip.count }}</span>
            </button>
        </div>
        <div class="search-palette__body">
            <div class="search-palette__loading" v-if="loader">
                <preloader></preloader>
            </div>
            <div class="search-empty" v-else-if="showMinChars">
                <i class="material-icons" aria-hidden="true">search</i>
                <p>@{{ i18n.minChars }}</p>
            </div>
            <div class="search-empty" v-else-if="showIdle">
                <i class="material-icons" aria-hidden="true">search</i>
                <p>@{{ i18n.noQuery }}</p>
            </div>
            <div class="search-empty" v-else-if="showNoResults">
                <i class="material-icons" aria-hidden="true">search_off</i>
                <p>@{{ noResultsLabel }}</p>
                <button type="button" class="btn-flat" @click="clearQuery">@{{ i18n.emptyCta }}</button>
            </div>
            <ul
                v-else
                id="search-palette-list"
                class="search-hit-list"
                role="listbox"
            >
                <li
                    v-for="(hit, index) in visibleHits"
                    :key="hit.id"
                    :id="'search-hit-' + index"
                    role="option"
                    :aria-selected="index === selectedIndex ? 'true' : 'false'"
                >
                    <search-hit
                        :hit="hit"
                        :active="index === selectedIndex"
                        @mouseenter.native="selectedIndex = index"
                    ></search-hit>
                </li>
            </ul>
        </div>
        <div class="search-palette__footer">
            <span>@{{ i18n.hint }}</span>
            <a
                v-if="queryTrimmed"
                class="search-palette__view-all"
                :href="resultsUrl"
                @click="closePalette"
            >@{{ i18n.viewAll }}</a>
        </div>
    </div>
</div>
