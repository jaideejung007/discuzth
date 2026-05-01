function mergeObjects(target, source) {
	for (const key in source) {
		if (source[key] && typeof source[key] === 'object' &&
		    target[key] && typeof target[key] === 'object') {
			mergeObjects(target[key], source[key]);
		} else {
			target[key] = source[key];
		}
	}
	return target;
}