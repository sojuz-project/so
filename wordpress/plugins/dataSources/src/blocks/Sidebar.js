import { PluginSidebar, PluginSidebarMoreMenuItem } from '@wordpress/edit-post';
import { __ } from '@wordpress/i18n';

const TITLE = __('Data sources');
const TARGET = 'dataSources';

export default (props) => (
  <>
    <PluginSidebarMoreMenuItem
      target={TARGET} // eslint-disable-line
      icon="download"
    >
      { TITLE }
    </PluginSidebarMoreMenuItem>
    <PluginSidebar
      name={TARGET}
      icon="download"
      title={TITLE}
      className="dataSources-sidebar"
    >
      {props.children}
    </PluginSidebar>
  </>
);