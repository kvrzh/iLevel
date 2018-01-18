class MainApp {
    static init() { // Инициализация приложения
        this.operations = document.querySelectorAll('.operation');
        this.table = document.querySelector('.operations tbody');
        this.generateUSD();
        this.events();
    }

    static generateUSD(el) {
        const ajax = new Ajax('GET', '/api/bank');
        ajax.send()
            .then((result) => {
                if (!result.data) {
                    throw new Error('No data');
                }
                const exchange = +result.data;
                if (el && el.classList[0] === 'operation') {
                    let uah = el.querySelector('.uah').innerHTML;
                    if (uah) {
                        let usd = el.querySelector('.usd');
                        if (usd) {
                            let value = uah / exchange;
                            usd.innerHTML = value.toFixed(2);
                        }
                    }
                } else {
                    for (let i = 0, len = this.operations.length; i < len; i++) {
                        let uah = this.operations[i].querySelector('.uah').innerHTML;
                        if (uah) {
                            let usd = this.operations[i].querySelector('.usd');
                            if (usd) {
                                let value = uah / exchange;
                                usd.innerHTML = value.toFixed(2);
                            }
                        }
                    }
                }
            })
            .catch((err) => {
                console.log(err);
            })
    } // Генерирование значения в долларах у всех операций, или у одной (указать в параметре)

    static generateCount() {
        let count = 0;
        const operations = document.querySelectorAll('.operation');
        for (let i = 0, len = operations.length; i < len; i++) {
            if (operations[i].classList[1] === 'loss') {
                count -= +operations[i].querySelector('.uah').innerText;
            } else {
                count += +operations[i].querySelector('.uah').innerText;
            }
        }
        return count.toFixed(2);
    } // Генерирование суммы всех операций

    static events() {
        this.table.addEventListener('click', (e) => {
            if (e.target.classList[0] === 'delete') {
                this.deleteOperation(e.target);
            }
            if (e.target.classList[0] === 'edit') {
                this.editOperation(e.target);
            }
        });
        document.querySelector('#create').addEventListener('submit', (e) => {
            e.preventDefault();
            this.createOperation();
        });
    } // Все обработчики событий

    static deleteOperation(el) {
        const id = el.getAttribute('data-id');
        let ajax = new Ajax('DELETE', '/api/operation/' + id);
        ajax.send()
            .then(() => {
                const parent = el.parentNode.parentNode;
                parent.remove();
            })
            .then(() => {
                const count = document.querySelector('.count');
                count.innerText = 'Итого: ' + this.generateCount();
            })
    } // Удаление операций

    static editOperation(el) {
        const parent = el.parentNode.parentNode;
        if (el.classList[el.classList.length - 1] === 'active') {
            el.classList.remove('active');
            const id = el.getAttribute('data-id');
            const data = {
                title: parent.querySelector('.title input').value,
                type: parent.querySelector('.type select').value,
                date: parent.querySelector('.date input').value,
                uah: parent.querySelector('.uah input').value,
            };
            const ajax = new Ajax('PUT', '/api/operation/' + id, data);
            ajax.send()
                .then((result) => {
                    parent.classList.remove(parent.classList[1]);
                    parent.querySelector('.title').innerHTML = data.title;
                    parent.querySelector('.type').innerHTML = data.type;
                    parent.querySelector('.date').innerHTML = data.date;
                    parent.querySelector('.uah').innerHTML = data.uah;
                    parent.classList.add(data.type);
                })
                .then(() => {
                    this.generateUSD(el);
                })
                .then(() => {
                    const count = document.querySelector('.count');
                    count.innerText = 'Итого: ' + this.generateCount();
                })
                .then(() => {
                    localStorage.removeItem('uah_' + el.getAttribute('data-id'));
                })
        } else {
            el.classList.add('active');
            const info = {
                title: parent.querySelector('.title').innerText,
                type: parent.querySelector('.type').innerText,
                date: parent.querySelector('.date').innerText,
                uah: parent.querySelector('.uah').innerText,
            };
            localStorage.setItem('uah_' + el.getAttribute('data-id'), info.uah);
            parent.querySelector('.title').innerHTML = '<input type="text" value="' + info.title + '">';
            parent.querySelector('.type').innerHTML = '<select><option value="profit">Profit</option><option value="loss">Loss</option></select>';
            parent.querySelector('.date').innerHTML = '<input type="date" value="' + info.date + '">';
            parent.querySelector('.uah').innerHTML = '<input type="text" value="' + info.uah + '">';
            parent.querySelector('.type').querySelector('select').value = info.type;
        }
    } // Изменение операций

    static createOperation() {
        const data = {
            title: document.querySelector('#title').value,
            type: document.querySelector('#type').value,
            date: document.querySelector('#date').value,
            uah: document.querySelector('#uah').value,
        };
        data.uah = parseFloat(data.uah).toFixed(2);
        const ajax = new Ajax('POST', '/api/operation', data);
        ajax.send()
            .then((result) => {
                result.data = JSON.parse(result.data);
                if (result.data.status && result.data.status === 'OK') {
                    let tr = document.createElement('tr');
                    tr.classList.add('operation');
                    tr.classList.add(data.type);
                    tr.innerHTML = '<td class="title">' + data.title + '</td><td class="type">' + data.type + '</td><td class="date">' + data.date + '</td><td class="uah">' + data.uah + '</td><td class="usd"></td><td class="actions" id="operation_' + result.data.id + '"> <button data-id="' + result.data.id + '" class="edit">Изменить</button> <button data-id="' + result.data.id + '" class="delete">Удалить</button> </td>';
                    this.table.insertBefore(tr, this.table.lastChild);
                    return tr;
                }
                return false;
            })
            .then((res) => {
                if (res !== false) {
                    this.generateUSD(res);
                }
            })
            .then(() => {
                const count = document.querySelector('.count');
                count.innerText = 'Итого: ' + this.generateCount();
            });
    } // Создание операций
}
class Ajax {
    constructor(method, url, data, now) {
        if (data) this.data = data;
        this.method = method;
        this.url = url;
    }

    send() {
        return new Promise((res, rej) => {
            const xhr = new XMLHttpRequest();
            xhr.open(this.method, this.url);
            xhr.setRequestHeader('Content-type', 'application/json; charset=utf-8');
            const data = this.data ? JSON.stringify(this.data) : null;
            xhr.send(data);
            xhr.onreadystatechange = () => {
                if (xhr.readyState != 4) return;
                if (xhr.status != 200) {
                    rej(xhr.status + ': ' + xhr.statusText);
                } else {
                    res({data: xhr.responseText});
                }
            }

        })
    }
} // Класс для отправки Ajax
window.addEventListener('DOMContentLoaded', () => {
    MainApp.init();
});
