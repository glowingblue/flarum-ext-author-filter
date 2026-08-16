import { extend } from 'flarum/common/extend';
import GlobalSearchState from 'flarum/forum/states/GlobalSearchState';

export default function extendGlobalSearchState() {
	extend(GlobalSearchState.prototype, 'stickyParams', function (this: GlobalSearchState, params) {
		params.author = m.route.param('author');
	});
}
