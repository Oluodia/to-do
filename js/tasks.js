let currentEditingTask = null

document.getElementById('taskForm').addEventListener('submit', function (event) {
	event.preventDefault()
	const title = document.getElementById('taskTitle').value
	const description = document.getElementById('taskDescription').value
	const status = document.getElementById('taskStatus').value

	if (title && description) {
		if (currentEditingTask) {
			// Редактирование существующей задачи
			currentEditingTask.querySelector('h5').textContent = title
			currentEditingTask.querySelector('p').textContent = description
			const statusElement = currentEditingTask.querySelector('.status-text')
			statusElement.textContent = status
			statusElement.setAttribute('data-status', status) // Обновляем атрибут data-status
			currentEditingTask = null
			document.getElementById('addTaskBtn').style.display = 'inline-block'
			document.getElementById('saveChangesBtn').style.display = 'none'
		} else {
			// Добавление новой задачи
			const taskItem = document.createElement('li')
			taskItem.className = 'list-group-item'
			taskItem.innerHTML = `
                <h5>${title}</h5>
                <p>${description}</p>
                <div class="status-dropdown">
                    <span class="status-text" data-status="${status}">${status}</span>
                    <button class="btn btn-sm btn-secondary float-right">Редактировать</button>
                    <button class="btn btn-sm btn-danger float-right mr-2">Удалить</button>
                </div>
            `

			document.getElementById('taskList').appendChild(taskItem)
		}

		document.getElementById('taskTitle').value = ''
		document.getElementById('taskDescription').value = ''
		document.getElementById('taskStatus').value = 'Активная'

		saveTasksToLocalStorage()
	}
})

document.getElementById('taskList').addEventListener('click', function (event) {
	if (event.target.classList.contains('btn-danger')) {
		// Удаление задачи
		event.target.parentElement.parentElement.remove()
		saveTasksToLocalStorage()
	} else if (event.target.classList.contains('btn-secondary')) {
		// Редактирование задачи
		const taskItem = event.target.parentElement.parentElement
		const title = taskItem.querySelector('h5').textContent
		const description = taskItem.querySelector('p').textContent
		const status = taskItem.querySelector('.status-text').textContent

		document.getElementById('taskTitle').value = title
		document.getElementById('taskDescription').value = description
		document.getElementById('taskStatus').value = status
		currentEditingTask = taskItem

		document.getElementById('addTaskBtn').style.display = 'none'
		document.getElementById('saveChangesBtn').style.display = 'inline-block'
	}
})

document.getElementById('saveChangesBtn').addEventListener('click', function () {
	document.getElementById('taskForm').dispatchEvent(new Event('submit'))
})