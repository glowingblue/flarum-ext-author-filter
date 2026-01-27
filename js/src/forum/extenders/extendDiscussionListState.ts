import app from 'flarum/forum/app';
import { extend } from 'flarum/common/extend';
import DiscussionListState from 'flarum/forum/states/DiscussionListState';

export default function extendDiscussionListState() {
	extend(DiscussionListState.prototype, 'requestParams', function (this: DiscussionListState, params: Record<string, any>) {
		const author = app.search.params().author;

		if (!author) return;

		params.filter.author = author;

		const q = params.filter.q;
		if (q) {
			params.filter.q = `${q} author:${author}`;
		}
	});
}
