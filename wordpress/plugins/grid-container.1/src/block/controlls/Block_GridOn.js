export default (props) => {
  return (
    <div className={`components-toolbar ${props.gridMode ? '' : 'active'}`}
      onClick={() => {
        props.setGridMode(prev => !prev)
        wp.data.dispatch("core/edit-post").openGeneralSidebar("edit-post/block")
      }}>
      <span className="dashicons dashicons-screenoptions"></span>
      {props.gridMode || <span class="grid-mode-button">Grid mode off</span>}
    </div>
  )
 }