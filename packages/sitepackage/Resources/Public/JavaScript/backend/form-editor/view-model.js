/**
 * Module: @brezo-it/sitepackage/backend/form-editor/view-model
 */

export function bootstrap(formEditorApp) {
    const subscriberIdentifier = 'view/stage/abstract/render/template/perform';

    formEditorApp.getPublisherSubscriber().subscribe(subscriberIdentifier, function(topic, args) {
        if (args[0] && args[0].get('type') === 'MultiImageUpload') {
            formEditorApp.getViewModel().getStage().renderSelectTemplates(args[0], args[1]);
        }
    });
}
