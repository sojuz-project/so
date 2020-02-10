const { Dropdown, Button } = wp.components;
export default (props) => {
  return (
    <Dropdown
      className="components-toolbar"
      contentClassName="my-popover-content-classname"
      position="bottom left"
      renderToggle={({ isOpen, onToggle }) => (
        <Button className="controll-button" onClick={onToggle} aria-expanded={isOpen}>
          <span className="dashicons dashicons-plus"></span>
        </Button>
      )}
      renderContent={() => (
        <div className="components-picker">
          <button
            className="components-button full-width is-button is-default"
            onClick={() => props.newComponent('coreheading')}>
            <span className="dashicons dashicons-editor-textcolor"></span> Heading
									</button>
          <button
            className="components-button full-width is-button is-default"
            onClick={() => props.newComponent('coreparagraph')}>
            <span className="dashicons dashicons-editor-paragraph"></span> Paragraph</button>
          <button
            className="components-button full-width is-button is-default"
            onClick={() => props.newComponent('customimage')}>
            <span className="dashicons dashicons-format-image"></span> 	Image</button>
          <button
            className="components-button full-width is-button is-default"
            onClick={() => props.newComponent('customwrapper')}>
            <span className="dashicons dashicons-admin-post"></span> Wrapper</button>
        </div>
      )}
    />
  )
 }