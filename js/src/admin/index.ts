import app from 'flarum/admin/app';
import AuthorFilterSettingsPage from './components/AuthorFilterSettingsPage';

app.initializers.add('glowingblue-author-filter', () => {
  app.extensionData.for('glowingblue-author-filter').registerPage(AuthorFilterSettingsPage);
});
