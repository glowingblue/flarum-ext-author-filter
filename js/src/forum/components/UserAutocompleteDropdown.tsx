import Component from 'flarum/common/Component';
import Button from 'flarum/common/components/Button';
import Dropdown from 'flarum/common/components/Dropdown';
import Separator from 'flarum/common/components/Separator';
import username from 'flarum/common/helpers/username';
import Avatar from 'flarum/common/components/Avatar';
import User from 'flarum/common/models/User';
import extractText from 'flarum/common/utils/extractText';
import app from 'flarum/forum/app';

import type Mithril from 'mithril';
import Stream from 'flarum/common/utils/Stream';
import LoadingIndicator from 'flarum/common/components/LoadingIndicator';

interface IAttrs {}

interface IState {
	currentData: User[];
	value: Stream<string>;
	searchQuery: Stream<string>;
	lastSearchedQuery: string;
	loading: boolean;
	timeoutKey: ReturnType<typeof setTimeout> | null;
	/** Slugs we have already tried to resolve, to avoid re-requesting on every redraw. */
	requestedSlugs: Set<string>;
}

const DEBOUNCE_TIME = 250;

export default class UserAutocompleteDropdown extends Component<IAttrs, IState> {
	oninit(vnode: Mithril.Vnode<IAttrs, this>): void {
		super.oninit(vnode);

		this.state = {
			currentData: [],
			value: Stream(''),
			searchQuery: Stream(''),
			lastSearchedQuery: '',
			loading: false,
			timeoutKey: null,
			requestedSlugs: new Set(),
		};
	}

	/**
	 * Resolve the user a slug refers to.
	 *
	 * The slug format depends on the configured user slug driver (username, id,
	 * or id-with-display-name), so it cannot be rendered as a name directly. If
	 * the user isn't in the store yet — e.g. on a fresh page load with `author`
	 * already in the URL — fetch them by slug.
	 */
	protected resolveUser(slug: string): User | undefined {
		const user = app.store.getBy<User>('users', 'slug', slug);

		if (user || this.state.requestedSlugs.has(slug)) return user;

		this.state.requestedSlugs.add(slug);

		app.store
			.find<User>('users', slug, { bySlug: true })
			.then(() => m.redraw())
			.catch(() => {
				// The slug matches no visible user; drop the filter.
				this.handleUserChange(null);
			});

		return undefined;
	}

	view() {
		this.performSearch(this.state.searchQuery());

		let content = [];

		if (this.state.loading) {
			content.push(<Separator />, <LoadingIndicator />);
		} else if (this.state.searchQuery().length < this.minSearchLength()) {
			this.state.lastSearchedQuery = '';
		} else if (!this.state.currentData?.length) {
			content.push(
				<Separator />,
				<span>{extractText(app.translator.trans('glowingblue-author-filter.forum.index_page.filter_user.no_results'))}</span>
			);
		} else {
			content.push(
				<Separator />,
				this.state.currentData?.map((user) => (
					<Button
						class="BlomstraUserFilter-item Button"
						onclick={() => {
							this.handleUserChange(user);
						}}
					>
						<Avatar user={user} /> {username(user)}
					</Button>
				))
			);
		}

		if (app.search.state.params().author) {
			// if author is set
			content.push(
				<Separator />,
				<Button class="Button" icon="fas fa-times" onclick={() => this.handleUserChange(null)}>
					{extractText(app.translator.trans('glowingblue-author-filter.forum.index_page.filter_user.remove_filter'))}
				</Button>
			);
		}

		return (
			<Dropdown
				buttonClassName="Button"
				label={this.label}
				accessibleToggleLabel={app.translator.trans('glowingblue-author-filter.forum.index_page.filter_user.accessible_label')}
				onshow={() => {
					this.$('input').trigger('focus');
				}}
			>
				<input
					type="text"
					class="FormControl"
					placeholder={extractText(app.translator.trans('glowingblue-author-filter.forum.index_page.filter_user.search_label'))}
					value={this.state.value()}
					oninput={(e: InputEvent) => {
						const value = (e.currentTarget as HTMLInputElement).value;

						this.state.value(value);

						this.state.timeoutKey && clearTimeout(this.state.timeoutKey);
						this.state.timeoutKey = setTimeout(() => {
							this.state.searchQuery(value);
							m.redraw();
						}, DEBOUNCE_TIME);
					}}
				/>

				{content}
			</Dropdown>
		);
	}

	protected minSearchLength(): number {
		const val = app.forum.attribute<number>('authorFilterMinSearchLength');

		return val > 0 ? val : 3;
	}

	protected maxResults(): number {
		const val = app.forum.attribute<number>('authorFilterMaxResults');

		return val > 0 ? val : 5;
	}

	async performSearch(query: string): Promise<void> {
		if (this.state.lastSearchedQuery === query) return;

		if (this.state.searchQuery().length < this.minSearchLength()) {
			this.state.currentData = [];
			return;
		}

		this.state.loading = true;
		this.state.lastSearchedQuery = query;
		m.redraw();

		const data = await app.store.find<User[]>('users', { filter: { q: query }, page: { limit: this.maxResults() } });

		// Prevent race conditions where a new search will finish before an old search
		if (this.state.searchQuery() !== query) return;

		this.state.currentData = data;

		this.state.loading = false;
		m.redraw();
	}

	handleUserChange(user: User | null) {
		const params = app.search.state.params();

		const old = params.author;

		if (!user) {
			delete params.author;
		} else {
			params.author = user.slug();
		}

		if (old !== params.author) {
			m.route.set(app.route(app.current.get('routeName'), { ...params }));
		}
	}

	get label() {
		function wrapLabel(text: Mithril.Children) {
			return app.translator.trans('glowingblue-author-filter.forum.index_page.filter_user.label', { text: <b>{text}</b> });
		}

		const author = app.search.state.params().author;

		if (author) {
			const slugs = author.split(',');
			const user = this.resolveUser(slugs[0]);

			// While the user is still being fetched, keep the filter intact and
			// show a neutral label rather than clearing the author.
			if (user) {
				return wrapLabel(user.displayName() + (slugs.length > 1 ? ` (+${slugs.length - 1})` : ''));
			}

			return wrapLabel(app.translator.trans('core.ref.loading'));
		}

		return wrapLabel(app.translator.trans('glowingblue-author-filter.forum.index_page.filter_user.all'));
	}
}
