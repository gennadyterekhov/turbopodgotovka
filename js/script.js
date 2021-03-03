'use strict';

const reactElement = React.createElement;

var config = {
    mainTableId: 'main-table',
    mainTableMessageId: 'main-table-message',
    addBtnId: 'add-btn',
    updateBtnId: 'update-btn',

    createFormUserIdInputId: 'create-form-user_id',
    createFormEventIdInputId: 'create-form-event_id',
    createFormEventTimeInputId: 'create-form-event_time',

    apiServer: 'http://localhost:8000/',
    apiFile: 'api/dbApi.php'
};



class MainComponent extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            tableData: getLogData(),
        };
    }
    render() {

        return (
            <TableComponent data={this.state.tableData} id={config.mainTableId} ></TableComponent>
        );
    }
}


class TableRowArrayComponent extends React.Component {
    constructor(props) {
        super(props);
        this.columns = props.columns;
        this.state = {};
    }
    render() {
        return (
            <tr>
                {
                    this.columns.map(
                        (value, index) => {
                            return <td key={index}>{value}</td>
                        }
                    )
                }
            </tr>
        );
    }
}

class ButtonComponent extends React.Component {
    constructor(props) {
        super(props);
        this.data = props;
        this.state = {};
    }
    render() {
        return (
            <button id={this.data.id} onClick={this.data.onClick}>{this.data.text}</button>
        );
    }
}


class TableRowObjectComponent extends React.Component {
    constructor(props) {
        super(props);
        this.data = props;
    }
    onChange(event) {
        console.log(event);
    }
    render() {
        return (
            <tr>

                <td>{this.data.index}</td>
                <td>{this.data.id}</td>
                <td>
                    <InputComponent type='number' onChange={this.onChange} value={this.data.user_id}></InputComponent>
                </td>
                <td>
                    <InputComponent type='number' onChange={this.onChange} value={this.data.event_id}></InputComponent>
                </td>
                <td>
                    <InputComponent type='text' onChange={this.onChange} value={this.data.event_time}></InputComponent>
                </td>
                <td><ButtonComponent text="[X]" onClick={this.data.deleteFunction} id={this.data.id}></ButtonComponent></td>
                
                
            </tr>
        );
    }
}


class InputComponent extends React.Component {
    constructor(props) {
        super(props);
        this.data = props;
        this.state = {
            value: props.value,
            innerHtml: '',
        };
        this.onChange = this.onChange.bind(this);
    }
    onChange(event) {
        console.log(event);
        console.log(event.target);
        this.setState(
            {
                value: event.target.value
            }
        );
        
    }
    render() {
        return (
            <input id={this.data.id} type={this.data.type} placeholder={this.data.placeholder} value={this.state.value} onChange={this.onChange}></input>
        );
    }
}

class CreateLogFormComponent extends React.Component {
    constructor(props) {
        super(props);
        this.data = props;
        this.state = {};
    }
    render() {
        return (
            <div>
                <InputComponent id={config.createFormUserIdInputId} type='number' placeholder='user_id'></InputComponent>
                <InputComponent id={config.createFormEventIdInputId}  type='number' placeholder='event_id'></InputComponent>
                <InputComponent id={config.createFormEventTimeInputId}  type='text' placeholder='event_time'></InputComponent>
            </div>
        );
    }
}


class TableComponent extends React.Component {
    constructor(props) {
        super(props);
        this.id = props.id;
        this.data = props.data;
        this.columns = [
            '#',
            'id',
            'user_id',
            'event_id',
            'event_time',
            '[delete]'
        ];
        this.state = {
            data: getLogData()
        };
        this.addBtnOnClick = this.addBtnOnClick.bind(this);
        this.updateBtnOnClick = this.updateBtnOnClick.bind(this);
        this.deleteLogEntry = this.deleteLogEntry.bind(this);
    }

    updateBtnOnClick() {
        console.log('updateBtnOnClick');
        const updatedTableData = this.state.data;

        updatedTableData.forEach((logEntry, index) => {
            let query = '?action=update&entity=log';
            let url = config.apiServer + config.apiFile + query;
            $.ajax({
                'async': true,
                'type': "POST",
                'global': false,
                dataType: 'json',
                'url': url,
                'data': {id:logEntry.id, user_id: logEntry.user_id, event_id: logEntry.event_id, event_time: logEntry.event_time},
            }).fail(function (data) {
                console.log('ajax fail');
                console.log(data);
            }).done(function (data) {
                console.log('ajax success');
                console.log(data);
            });
        });

        this.setState(
            {
                data: getLogData()
            }
        );
        this.forceUpdate();
    };

    createLog(data, component) {
        let query = '?action=create&entity=log';
        let url = config.apiServer + config.apiFile + query;
        $.ajax({
            'async': true,
            'type': "POST",
            'global': false,
            dataType: 'json',
            'url': url,
            'data': {user_id: data.userId, event_id: data.eventId, event_time: data.eventTime},
        }).fail(function (data) {
            console.log('ajax fail');
            console.log(data);
        }).done(function (data) {
            console.log('ajax success');
            console.log(data);
            component.setState(
                {
                    data: getLogData()
                }
            );
            component.forceUpdate();
        });

    };

    addBtnOnClick() {
        console.log('addBtnOnClick');
        // беру данные из формы
        const data = getDataFromForm();
        // отправляю запрос за добавление лога
        this.createLog(data, this);
    };

    deleteLogEntry(event) {
        const logId = event.target.id;
        let query = '?action=delete&entity=log';
        let url = config.apiServer + config.apiFile + query;
        const component = this;
        $.ajax({
            'async': true,
            'type': "POST",
            'global': false,
            dataType: 'json',
            'url': url,
            // 'data': { 'entity': 'log', 'action': 'select' },
            'data': {id: logId},
        }).fail(function (data) {
            console.log('ajax fail');
            console.log(data);
        }).done(function (data) {
            console.log('ajax success');
            console.log(data);
            component.setState(
                {
                    data: getLogData()
                }
            );
            component.forceUpdate();
        });
    }

    render() {

        return (
            <div>

                <table id={this.id}>
                    <thead>
                        <TableRowArrayComponent columns={this.columns} ></TableRowArrayComponent>
                    </thead>
                    <tbody>
                        
                        {
                            this.state.data.map(
                                (dataValue, dataIndex) => {
                                    return (
                                        
                                        <TableRowObjectComponent
                                            key={dataIndex}
                                            index={dataIndex + 1}
                                            id={dataValue.id}
                                            user_id={dataValue.user_id}
                                            event_id={dataValue.event_id}
                                            event_time={dataValue.event_time}
                                            deleteFunction={this.deleteLogEntry}
                                        ></TableRowObjectComponent>
                                    )
                                }
                            )
                        }

                    </tbody>
                </table>
                <ButtonComponent text="Обновить" onClick={this.updateBtnOnClick}></ButtonComponent>
                <hr></hr>
                Добавить: <CreateLogFormComponent ></CreateLogFormComponent>
                <ButtonComponent text="Добавить" onClick={this.addBtnOnClick}></ButtonComponent>
            </div>
        );
    }
}


function getLogData() {
    var logData = {};
    let url = config.apiServer + config.apiFile;
    $.ajax({
        'async': false,
        'type': "GET",
        'global': false,
        'url': url,
        'data': { 'entity': 'log', 'action': 'select' },
    }).fail(function (data) {
        console.log('ajax fail');
        console.log(data);
    }).done(function (data) {
        console.log('ajax success');
        console.log(data);
        logData = data;
    });
    return logData;
}


function getDataFromForm() {
    const userIdInput = $(`#${config.createFormUserIdInputId}`);
    const eventIdInput = $(`#${config.createFormEventIdInputId}`);
    const eventTimeInput = $(`#${config.createFormEventTimeInputId}`);

    const userId = userIdInput[0].value;
    const eventId = eventIdInput[0].value;
    const eventTime = eventTimeInput[0].value;
    const data = {
        userId,
        eventId,
        eventTime
    };
    return data;
};


var updateTable = function (logData) {
    let table = $(`#${config.mainTableId}`);
    let tableHtml = '';

    if (logData.length == 0) {
        // данные пустые
        let tableMessage = $(`#${config.mainTableMessageId}`);
        let message = 'данных нет';
        tableMessage.append(message);
        return;
    }

    for (let i = 0; i < logData.length; i += 1) {
        let rowHtml = `
            <tr>
                <td>
                    ${i + 1}
                </td>
                <td>
                    <input type="number" value="${logData[i].id}">
                </td>
                <td>
                    <input type="number" value="${logData[i].user_id}">
                </td>
                <td>
                    <input type="number" value="${logData[i].event_id}">
                </td>
                <td>
                    <input type="text" value="${logData[i].event_time}">
                </td>
            </tr>
        `;
        tableHtml += rowHtml;
    }

    table.append(tableHtml);

};

var getDataFromTable = function () {
    const userIdInput = $(`#${config.createFormUserIdInputId}`);
    const eventIdInput = $(`#${config.createFormEventIdInputId}`);
    const eventTimeInput = $(`#${config.createFormEventTimeInputId}`);

    const userId = userIdInput[0].value;
    const eventId = eventIdInput[0].value;
    const eventTime = eventTimeInput[0].value;
    const data = {
        userId,
        eventId,
        eventTime
    };
    return data;
};

var main = function () {
    const domContainer = document.querySelector('#react-root');
    ReactDOM.render(reactElement(MainComponent), domContainer);
}

main();
