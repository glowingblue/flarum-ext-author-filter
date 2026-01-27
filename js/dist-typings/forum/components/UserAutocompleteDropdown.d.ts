/// <reference types="flarum/@types/translator-icu-rich" />
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
    timeoutKey: number | null;
}
export default class UserAutocompleteDropdown extends Component<IAttrs, IState> {
    oninit(vnode: Mithril.Vnode<IAttrs, this>): void;
    view(): JSX.Element;
    protected minSearchLength(): number;
    protected maxResults(): number;
    performSearch(query: string): Promise<void>;
    handleUserChange(user: User | null): void;
    get label(): import("@askvortsov/rich-icu-message-formatter").NestedStringArray;
}
export {};
