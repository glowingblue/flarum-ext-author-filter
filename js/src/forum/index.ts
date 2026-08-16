import app from 'flarum/forum/app';

import addUserFilterDropdownToIndexPage from './extenders/addUserFilterDropdownToIndexPage';
import extendDiscussionListState from './extenders/extendDiscussionListState';
import extendGlobalSearchState from './extenders/extendGlobalSearchState';

app.initializers.add('glowingblue-author-filter', () => {
  addUserFilterDropdownToIndexPage();
  extendDiscussionListState();
  extendGlobalSearchState();
});
