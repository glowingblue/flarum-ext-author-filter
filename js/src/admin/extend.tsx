import app from 'flarum/admin/app';
import Extend from 'flarum/common/extenders';

export default [
	new Extend.Admin()
		.setting(() => ({
			label: app.translator.trans('glowingblue-author-filter.admin.settings.min_length'),
			type: 'number',
			setting: 'glowingblue-author-filter.min_search_length',
			placeholder: 3,
		}))
		.setting(() => ({
			label: app.translator.trans('glowingblue-author-filter.admin.settings.result_count'),
			type: 'number',
			setting: 'glowingblue-author-filter.max_results',
			placeholder: 5,
		}))
		.customSetting(() => <p className="helpText">{app.translator.trans('glowingblue-author-filter.admin.settings.intro')}</p>),
];
