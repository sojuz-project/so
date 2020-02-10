import React from "react";
import GridLayout from 'react-grid-layout';


const MyFirstGrid = ({
  layout,
  onChange = () => null,
  // onAdd = () => null,
  blocks,
  props,
  template,
  selectedElement,
  onRemoveItem,
  onClickItem,
  ...rest,
}) => {
  
    return (
      <React.Fragment>
        <GridLayout 
          className="layout" 
          layout={layout} 
          cols={12} 
          rowHeight={30} 
          width={236} 
          margin={[5,5]}
          containerPadding={[0, 0]}
          onLayoutChange={onChange}
        >
          {layout.map((el,i) => (
            <div 
              onClick={() => onClickItem(i)}
              key={el.i}
              className={selectedElement.componentI == i && 'component-selected'}
              style={ selectedElement.componentI == i && {
                border: '2px solid #56b5da',
                
              }}>
              
              <div>{(template[0][i]) ? template[0][i].blockName : ''}</div>
              <svg>
                 
                {template[0][i].blockName == 'coreimage' && <path d="M 2.37 3.35 L 2.37 167 L 207 167 L 207 3.35 L 2.37 3.35 z"></path>}
                {template[0][i].blockName == 'coreheading' && <path d="M 2.3710938 6.0605469 L 2.3710938 18.931641 L 206.61328 18.931641 L 206.61328 6.0605469 L 2.3710938 6.0605469 z M 2.3710938 26.039062 L 2.3710938 38.910156 L 206.61328 38.910156 L 206.61328 26.039062 L 2.3710938 26.039062 z M 2.3710938 46.757812 L 2.3710938 57.935547 L 165.29102 57.935547 L 165.29102 46.757812 L 2.3710938 46.757812 z "></path> }
                {template[0][i].blockName == 'coreparagraph' && <path d="M 1.94,1.19 V 4.86 H 206 V 1.19 Z m 0,6 v 3.66 H 206 V 7.19 Z m 0,6 v 3.66 H 98.0 v -3.66 z m 0,6.05 v 3.66 H 98.0 v -3.66 z m 0,6 v 3.66 H 125 v -3.66 z m 0,6 v 3.66 H 163 v -3.66 z m 0,6 v 3.66 H 163 v -3.66 z m 0,12 v 3.66 H 70.5 v -3.66 z m 0,6 v 3.66 H 70.59 v -3.66 z m 0,6 v 3.66 H 47.24 v -3.66 z m 0,7.88 v 3.66 H 206 v -3.66 z m 0,6 v 3.66 H 206 v -3.66 z m 0,6 v 3.66 H 163 v -3.66 z m 0,6 v 3.66 H 163 v -3.66 z m 0,12 v 3.66 H 206 v -3.66 z m 0,5.99 v 3.66 H 206 v -3.66 z m 0,6 v 3.66 H 98.0 v -3.66 z"></path>}
              </svg>
              <span
                className="remove"
                onClick={() => onRemoveItem(i)}
              >
                x
              </span>
            </div>
          ))}
        </GridLayout>
        {/* <button
          className="components-button components-color-palette__clear is-button is-default"
          type="button" onClick={onAdd}>+ Add grid cell</button> */}
      </React.Fragment>
    )
}

export default MyFirstGrid;