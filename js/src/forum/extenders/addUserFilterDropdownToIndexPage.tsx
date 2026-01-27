import app from 'flarum/forum/app';
import { extend } from 'flarum/common/extend';
import IndexPage from 'flarum/forum/components/IndexPage';
import UserAutocompleteDropdown from '../components/UserAutocompleteDropdown';

import type ItemList from 'flarum/common/utils/ItemList';
import type Mithril from 'mithril';

export default function addUserFilterDropdownToIndexPage() {
	extend(IndexPage.prototype, 'viewItems', function (items: ItemList<Mithril.Children>) {
		if (app.current.get('routeName') === 'byobuPrivate') return;

		if (!!app.forum.attribute('canUseBlomstraUserFilter')) {
			items.add('userFilter', <UserAutocompleteDropdown />, -15);
		}
	});
}
