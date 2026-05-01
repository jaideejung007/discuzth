document.querySelectorAll('.dragObj').forEach(item => {
	item.addEventListener('dragstart', dragStart);
	item.addEventListener('dragend', dragEnd);
});

document.getElementById('show_widgets_left').addEventListener('dragover', dragOver);
document.getElementById('show_widgets_left').addEventListener('drop', drop);
document.getElementById('show_widgets_right').addEventListener('dragover', dragOver);
document.getElementById('show_widgets_right').addEventListener('drop', drop);

function dragStart(e) {
	e.dataTransfer.effectAllowed = 'move';
	e.dataTransfer.setData('text/html', this.innerHTML);
	this.classList.add('dragging');
}

function dragEnd(e) {
	this.classList.remove('dragging');
}

function dragOver(e) {
	e.preventDefault();
	e.dataTransfer.dropEffect = 'move';
}

function drop(e) {
	e.preventDefault();
	const source = document.querySelector('.dragging');
	const target = e.target.closest('.dragObj');

	if (target && source !== target) {
		const sourceIndex = Array.from(source.parentNode.children).indexOf(source);
		const targetIndex = Array.from(target.parentNode.children).indexOf(target);

		if (sourceIndex < targetIndex) {
			target.parentNode.insertBefore(source, target.nextSibling);
		} else {
			target.parentNode.insertBefore(source, target);
		}
	}
}