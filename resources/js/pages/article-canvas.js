import { createPersistenceActions } from './article-canvas/persistence.js';
import { createFormattingActions } from './article-canvas/formatting.js';
import { createContextualActions } from './article-canvas/contextual.js';
import { createBlockActions } from './article-canvas/blocks.js';
import { createImageCodeActions } from './article-canvas/image-code.js';
import { createEmbedActions } from './article-canvas/embeds.js';
import { createCategoryActions } from './article-canvas/categories.js';
import { createPublishingActions } from './article-canvas/publishing.js';
import { createShortcutActions } from './article-canvas/shortcuts.js';
import { wireDocuments } from './article-canvas/wire-documents.js';
import { wireToolbar } from './article-canvas/wire-toolbar.js';
import { wireImages } from './article-canvas/wire-images.js';
import { wireGlobalEvents } from './article-canvas/wire-global.js';

const root = document.querySelector('[data-article-canvas]');

if (root) {
    mountCanvas(root);
}

function mountCanvas(app) {
    const context = {
        app,
        csrf: document.querySelector('meta[name="csrf-token"]')?.content || '',
        documents: Array.from(app.querySelectorAll('[data-document-language]')),
        languageButtons: Array.from(app.querySelectorAll('[data-language]')),
        saveState: app.querySelector('[data-save-state]'),
        countToggle: app.querySelector('[data-count-toggle]'),
        countPopover: app.querySelector('[data-count-popover]'),
        wordCount: app.querySelector('[data-word-count]'),
        characterCount: app.querySelector('[data-character-count]'),
        inlineToolbar: app.querySelector('[data-inline-toolbar]'),
        inlineColors: app.querySelector('[data-inline-colors]'),
        linkInput: app.querySelector('[data-link-input]'),
        blockMenu: app.querySelector('[data-block-menu]'),
        blockToggle: app.querySelector('[data-block-toggle]'),
        blockActions: app.querySelector('[data-block-actions]'),
        imageInput: app.querySelector('[data-image-file]'),
        thumbnailInput: app.querySelector('[data-thumbnail-file]'),
        imageToolbar: app.querySelector('[data-image-toolbar]'),
        codeToolbar: app.querySelector('[data-code-toolbar]'),
        publishDrawer: app.querySelector('[data-publish-drawer]'),
        textColorClasses: ['muted', 'green', 'blue', 'red', 'amber'],
        backgroundClasses: ['gray', 'yellow', 'green', 'blue', 'rose'],
        imageLayoutClasses: ['compact', 'inline', 'outset', 'screen'],
        imageAlignClasses: ['left', 'center', 'right'],
    };
    const state = {
        activeLanguage: 'id',
        savedRange: null,
        selectedFigure: null,
        selectedCodeBlock: null,
        imageUploadIntent: 'insert',
        autosaveTimer: null,
        dirty: false,
        saving: false,
        currentWords: 0,
        currentCharacters: 0,
        thumbnailUrl: app.dataset.thumbnailUrl || '',
    };
    context.activeDocument = () => context.documents.find(
        (document) => document.dataset.documentLanguage === state.activeLanguage,
    );
    context.activeEditor = () => context.activeDocument()?.querySelector('[data-editor]');

    const actions = {};
    Object.assign(actions, createPersistenceActions(context, state, actions));
    Object.assign(actions, createFormattingActions(context, state, actions));
    Object.assign(actions, createContextualActions(context, state, actions));
    Object.assign(actions, createBlockActions(context, state, actions));
    Object.assign(actions, createImageCodeActions(context, state, actions));
    Object.assign(actions, createEmbedActions(context, state, actions));
    Object.assign(actions, createCategoryActions(context, state, actions));
    Object.assign(actions, createPublishingActions(context, state, actions));
    Object.assign(actions, createShortcutActions(context, state, actions));

    wireDocuments(context, state, actions);
    wireToolbar(context, state, actions);
    wireImages(context, state, actions);
    actions.setupUrlDialog();
    actions.setupUnsplash();
    const categoryManager = actions.setupCategories();
    actions.setupPublish(categoryManager);
    wireGlobalEvents(context, state, actions);
    actions.updateMetrics();
}
