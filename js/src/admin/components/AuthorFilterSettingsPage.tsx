import Form from 'flarum/common/components/Form';
import app from 'flarum/admin/app';
import ExtensionPage from 'flarum/admin/components/ExtensionPage';

export default class AuthorFilterSettingsPage extends ExtensionPage {
  content() {
    return (
      <div className="ExtensionPage-settings AuthorFilterSettingsPage">
        <div className="container">
          <Form>
            {this.buildSettingComponent({
              label: app.translator.trans('glowingblue-author-filter.admin.settings.min_length'),
              type: 'number',
              setting: 'glowingblue-author-filter.min_search_length',
              placeholder: 3,
            })}
            {this.buildSettingComponent({
              label: app.translator.trans('glowingblue-author-filter.admin.settings.result_count'),
              type: 'number',
              setting: 'glowingblue-author-filter.max_results',
              placeholder: 5,
            })}
            <p className="helpText">{app.translator.trans('glowingblue-author-filter.admin.settings.intro')}</p>
            {this.submitButton()}
          </Form>
        </div>
      </div>
    );
  }
}
