import app from 'flarum/forum/app';
import { extend } from 'flarum/common/extend';
import DiscussionListState from 'flarum/forum/states/DiscussionListState';

export default function extendDiscussionListState() {
	extend(DiscussionListState.prototype, 'requestParams', function (this: DiscussionListState, params: Record<string, any>) {
		const author = app.search.state.params().author;

		if (!author) return;

		// Core's AuthorGambit owns the `q` representation of this filter, so we
		// only need to set the filter itself.
		params.filter.author = author;
	});
}
