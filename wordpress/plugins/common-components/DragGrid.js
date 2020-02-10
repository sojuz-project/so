import React from "react";
import { Rnd } from "react-rnd";

const BREAKPOINTS = 50;
const GRID_ARRAY = Array.from({ length: BREAKPOINTS }, (_, k) => k);
const calculateCellW = (w, wModuloBreakpoint) => (wModuloBreakpoint ? w - (BREAKPOINTS - wModuloBreakpoint) : w) / BREAKPOINTS;
const resizeHandleClasses = {
  bottom: 'resize-handler bottom',
  left: 'resize-handler left',
  right: 'resize-handler right',
  top: 'resize-handler top',
};

export const DragGrid = ({
  gridMode,
  children,
  techC,
  techS,
  styleComponent,
  styleSection,
  onChange = () => null,
}) => {
  const scaleDown = 1.5;
  const ref = React.useRef();
  const [startW, setBlockWidth] = React.useState(null);
  const cellW = React.useMemo(() => calculateCellW(startW, startW % BREAKPOINTS), [startW]);
  const dragGridStyle = React.useMemo(() => gridMode ? styleSection : {
    width: startW,
    height: `${techS.h * cellW}px`,
    position: 'relative',
    justifyContent: 'center',
  }, [gridMode, techS.h, cellW, startW]);

  React.useEffect(() => {
    setBlockWidth(ref.current.getBoundingClientRect().width / scaleDown);
  }, [gridMode])

  return (
    <div ref={ref}>
      <div className="blueprint-grid" style={dragGridStyle}>
        {
          
          GRID_ARRAY.map(el => (
          <React.Fragment key={el}>
            { el <= techS.h && <div className={`row i${el}`} style={{ top: `${el * cellW}px` }}></div>}
            <div className={`column i${el}`} style={{ left: `${el * cellW}px` }}></div>
          </React.Fragment>
          
        ))}

      </div>
      <div
        className="drag-greed"
        style={dragGridStyle}
      >
        {gridMode ? children : children.map((child, childI) => (
          <Rnd
            style={!gridMode && styleComponent[childI]}
            enableUserSelectHack={false}
            key={`key-${childI}`}
            dragGrid={[cellW, cellW]}
            resizeGrid={[cellW, cellW]}
            size={{
              width: techC[childI].w * cellW,
              height: techC[childI].h * cellW,
            }}
            position={{
              x: techC[childI].x * cellW,
              y: techC[childI].y * cellW,
            }}
            resizeHandleClasses={resizeHandleClasses}
            onDrag={(_, { x, y, node: { clientHeight, clientWidth } }) => {
              onChange(
                techC.map((cmp, cmpI) => {
                  if (cmpI !== childI) return cmp;

                  const roundX = Math.round(x / cellW);
                  const roundX2 = Math.round(x / cellW + clientWidth / cellW);
                  const roundY = Math.round(y / cellW);
                  const roundY2 = Math.round(y / cellW + clientHeight / cellW);

                  return {
                    ...cmp,
                    x: roundX < 0 ? 0 : roundX2 >= BREAKPOINTS ? BREAKPOINTS - (roundX2 - roundX) - 1 : roundX,
                    x2: roundX2 < BREAKPOINTS ? roundX2 : BREAKPOINTS - 1,
                    y: roundY >= 0 ? roundY : 0,
                    y2: roundY >= 0 ? roundY2 : roundY2 - roundY, // roundY can be negative here
                  };
                }
              ));
            }}
            onResize={(...args) => {
              if (args[1] === 'bottom') {
                onChange(
                  techC.map((cmp, cmpI) => {
                    if (cmpI !== childI) return cmp;
                    return {
                      ...cmp,
                      y2: Math.round(args[4].y / cellW + cmp.h + (args[3].height / cellW)),
                    }
                  })
                )
              }
            }}
            onResizeStop={(...args) =>  onChange(
              techC.map(
                (cmp, cmpI) => {
                  if (cmpI !== childI) return cmp;
                  const roundX = Math.round(args[4].x / cellW);
                  const roundX2 = Math.round(args[4].x / cellW + cmp.w + (args[3].width / cellW));
                  const roundY = Math.round(args[4].y / cellW);
                  const roundY2 = Math.round(args[4].y / cellW + cmp.h + (args[3].height / cellW));
                  const newW = Math.round(cmp.w + (args[3].width / cellW));
                  const newH = Math.round(cmp.h + (args[3].height / cellW));
                  return {
                    ...cmp,
                    x: roundX < 0 ? 0 : roundX,
                    y: roundY < 0 ? 0 : roundY,
                    // important here + roundX is negative
                    w: roundX < 0 ? newW + roundX : roundX2 < BREAKPOINTS ? newW : BREAKPOINTS - roundX - 1,
                    // important here + roundY is negative
                    h: roundY < 0 ? newH + roundY : newH,
                    x2: roundX2 < BREAKPOINTS ? roundX2 : BREAKPOINTS - 1,
                    y2: roundY2,
                  }
                }
              ))}
            >
              {child}
            </Rnd>
        ))}
      </div>
    </div>
  )
}
