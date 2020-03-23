import { useEffect, useRef, useState } from 'react';

export const useUndo = ({ setAttributes, attributes }) => {
	const historyRef = useRef([attributes]);
	const [index, setIndex] = useState(0);

	useEffect(() => {
		
		const onKeyDown = ({ keyCode, ctrlKey, shiftKey }) => {
			if (ctrlKey && keyCode == 90) {
				if (!shiftKey && index > 0) {
					setIndex(index - 1);
				} else if (shiftKey && index < historyRef.current.length - 1) {
					setIndex(index + 1);
				}
			}
		};

		window.addEventListener('keydown', onKeyDown);

		return () => window.removeEventListener('keydown', onKeyDown);
	}, [index]);

	const setNewState = (nextState) => {
		const newState = { ...historyRef.current[index], ...nextState };
		const newIndex = index + 1;
		historyRef.current[newIndex] = newState;
		setIndex(newIndex);
		setAttributes(newState);
	};

	return { attributes: historyRef.current[index], setAttributes: setNewState };
};
