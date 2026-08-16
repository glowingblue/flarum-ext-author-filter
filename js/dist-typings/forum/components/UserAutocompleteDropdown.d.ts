import Component from 'flarum/common/Component';
import User from 'flarum/common/models/User';
import type Mithril from 'mithril';
import Stream from 'flarum/common/utils/Stream';
interface IAttrs {
}
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
export default class UserAutocompleteDropdown extends Component<IAttrs, IState> {
    oninit(vnode: Mithril.Vnode<IAttrs, this>): void;
    /**
     * Resolve the user a slug refers to.
     *
     * The slug format depends on the configured user slug driver (username, id,
     * or id-with-display-name), so it cannot be rendered as a name directly. If
     * the user isn't in the store yet — e.g. on a fresh page load with `author`
     * already in the URL — fetch them by slug.
     */
    protected resolveUser(slug: string): User | undefined;
    view(): JSX.Element;
    protected minSearchLength(): number;
    protected maxResults(): number;
    performSearch(query: string): Promise<void>;
    handleUserChange(user: User | null): void;
    get label(): any[];
}
export {};
