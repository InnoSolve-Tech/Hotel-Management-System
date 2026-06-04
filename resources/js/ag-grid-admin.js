const {
    AllCommunityModule,
    ModuleRegistry,
    createGrid,
} = require("ag-grid-community");

ModuleRegistry.registerModules([AllCommunityModule]);

(function initializeHotelioAgGrid() {
    const jquery = window.jQuery || window.$;

    if (!jquery) {
        return;
    }

    jquery.noConflict = function noConflict() {
        window.$ = jquery;
        window.jQuery = jquery;

        return jquery;
    };

    window.$ = jquery;
    window.jQuery = jquery;

    const gridInstances = new WeakMap();

    function makeSafeName(value, fallback) {
        return String(value || fallback || "hotelio-list")
            .replace(/[^a-z0-9]+/gi, "-")
            .replace(/^-|-$/g, "")
            .toLowerCase();
    }

    function escapeHtml(value) {
        const holder = document.createElement("div");
        holder.textContent =
            value === null || value === undefined ? "" : String(value);

        return holder.innerHTML;
    }

    function looksLikeHtml(value) {
        return typeof value === "string" && /<\/?[a-z][\s\S]*>/i.test(value);
    }

    function getNestedValue(source, path) {
        if (!path || source === null || source === undefined) {
            return source;
        }

        return String(path)
            .split(".")
            .reduce(function reducePath(currentValue, key) {
                return currentValue === null || currentValue === undefined
                    ? undefined
                    : currentValue[key];
            }, source);
    }

    function getHeadersFromTable(tableElement) {
        return Array.from(tableElement.querySelectorAll("thead th")).map(
            function mapHeader(header, index) {
                return header.textContent.trim() || `Column ${index + 1}`;
            },
        );
    }

    function getFilename(options, tableElement) {
        const configuredButton = Array.isArray(options.buttons)
            ? options.buttons.find(function findButton(button) {
                  return button && button.filename;
              })
            : null;

        return makeSafeName(
            configuredButton && configuredButton.filename,
            tableElement.id ||
                tableElement.getAttribute("data-grid-name") ||
                document.title,
        );
    }

    function normalizeColumnDefinitions(options, tableElement) {
        const headers = getHeadersFromTable(tableElement);
        const configuredColumns =
            Array.isArray(options.columns) && options.columns.length
                ? options.columns
                : headers.map(function mapHeaderToColumn(header) {
                      return {
                          data: /^action$/i.test(header) ? "action" : header,
                          title: header,
                      };
                  });

        return configuredColumns.map(function mapColumn(column, index) {
            const headerName =
                column.title ||
                column.name ||
                headers[index] ||
                column.data ||
                `Column ${index + 1}`;
            const fieldName =
                column.data || makeSafeName(headerName, `column-${index + 1}`);
            const isActionColumn =
                fieldName === "action" || /action/i.test(headerName);

            return {
                headerName: headerName,
                field: fieldName,
                hide: column.visible === false,
                sortable: !isActionColumn,
                filter: !isActionColumn,
                resizable: true,
                minWidth: isActionColumn ? 230 : 130,
                maxWidth: isActionColumn ? 300 : undefined,
                flex: isActionColumn ? 0 : 1,
                pinned: isActionColumn ? "right" : undefined,
                lockPinned: isActionColumn,
                suppressSizeToFit: isActionColumn,
                cellClass: isActionColumn
                    ? "hotelio-ag-cell-actions"
                    : undefined,
                cellRenderer: function renderCell(params) {
                    const rawValue = getNestedValue(params.data, fieldName);
                    const renderedValue =
                        typeof column.render === "function"
                            ? column.render(rawValue, "display", params.data)
                            : rawValue;

                    if (looksLikeHtml(renderedValue)) {
                        const wrapper = document.createElement("div");
                        wrapper.className = isActionColumn
                            ? "hotelio-ag-actions"
                            : "";
                        wrapper.innerHTML = renderedValue || "";

                        return wrapper;
                    }

                    return escapeHtml(renderedValue);
                },
            };
        });
    }

    function parseStaticRows(tableElement, columnDefinitions) {
        return Array.from(tableElement.querySelectorAll("tbody tr")).map(
            function mapRow(row) {
                const cells = Array.from(row.children);

                return columnDefinitions.reduce(function reduceCells(
                    result,
                    columnDefinition,
                    index,
                ) {
                    result[columnDefinition.field] = cells[index]
                        ? cells[index].innerHTML.trim()
                        : "";

                    return result;
                }, {});
            },
        );
    }

    function extractRows(response) {
        if (Array.isArray(response)) {
            return response;
        }

        if (response && Array.isArray(response.data)) {
            return response.data;
        }

        return [];
    }

    function getVisibleRows(gridApi) {
        const rows = [];

        gridApi.forEachNodeAfterFilterAndSort(function collectNode(node) {
            rows.push(Object.assign({}, node.data));
        });

        return rows;
    }

    function getExportMatrix(gridApi, columnDefinitions) {
        const visibleColumns = columnDefinitions.filter(
            function filterVisible(columnDefinition) {
                return !columnDefinition.hide;
            },
        );
        const body = getVisibleRows(gridApi).map(function mapRow(row) {
            return visibleColumns.map(function mapCell(columnDefinition) {
                const value = getNestedValue(row, columnDefinition.field);
                const holder = document.createElement("div");
                holder.innerHTML =
                    value === null || value === undefined ? "" : String(value);

                return holder.textContent.trim();
            });
        });

        return {
            header: visibleColumns.map(function mapHeader(columnDefinition) {
                return columnDefinition.headerName;
            }),
            body: body,
        };
    }

    function downloadBlob(blob, filename) {
        const anchor = document.createElement("a");
        const url = URL.createObjectURL(blob);
        anchor.href = url;
        anchor.download = filename;
        document.body.appendChild(anchor);
        anchor.click();
        document.body.removeChild(anchor);
        URL.revokeObjectURL(url);
    }

    function copyGrid(gridApi, columnDefinitions) {
        const matrix = getExportMatrix(gridApi, columnDefinitions);
        const text = [matrix.header]
            .concat(matrix.body)
            .map(function mapLine(line) {
                return line.join("\t");
            })
            .join("\n");

        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(text);
            return;
        }

        const textarea = document.createElement("textarea");
        textarea.value = text;
        document.body.appendChild(textarea);
        textarea.select();
        document.execCommand("copy");
        document.body.removeChild(textarea);
    }

    function exportJson(gridApi, filename) {
        downloadBlob(
            new Blob([JSON.stringify(getVisibleRows(gridApi), null, 2)], {
                type: "application/json",
            }),
            `${filename}.json`,
        );
    }

    function exportPdf(gridApi, columnDefinitions, filename) {
        if (!window.pdfMake) {
            exportJson(gridApi, filename);
            return;
        }

        const matrix = getExportMatrix(gridApi, columnDefinitions);

        window.pdfMake
            .createPdf({
                pageOrientation: "landscape",
                content: [
                    { text: filename.replace(/-/g, " "), style: "header" },
                    {
                        table: {
                            headerRows: 1,
                            widths: matrix.header.map(function mapWidth() {
                                return "*";
                            }),
                            body: [matrix.header].concat(matrix.body),
                        },
                    },
                ],
                styles: {
                    header: {
                        bold: true,
                        fontSize: 16,
                        margin: [0, 0, 0, 12],
                    },
                },
            })
            .download(`${filename}.pdf`);
    }

    function createToolbar(gridApi, columnDefinitions, filename, reloadRows) {
        const toolbar = document.createElement("div");
        toolbar.className = "hotelio-grid-toolbar";
        toolbar.innerHTML = [
            '<input type="search" class="form-control hotelio-grid-search" placeholder="Search table">',
            '<div class="hotelio-grid-toolbar__actions">',
            '<button type="button" class="btn btn-sm bg-navy" data-grid-action="reset">Reset</button>',
            '<button type="button" class="btn btn-sm btn-info" data-grid-action="copy">Copy</button>',
            '<button type="button" class="btn btn-sm btn-success" data-grid-action="csv">CSV</button>',
            '<button type="button" class="btn btn-sm btn-warning" data-grid-action="json">JSON</button>',
            '<button type="button" class="btn btn-sm bg-purple" data-grid-action="pdf">PDF</button>',
            "</div>",
        ].join("");

        toolbar
            .querySelector(".hotelio-grid-search")
            .addEventListener("input", function handleSearch(event) {
                gridApi.setGridOption("quickFilterText", event.target.value);
            });

        toolbar.addEventListener("click", function handleToolbarClick(event) {
            const button = event.target.closest("[data-grid-action]");

            if (!button) {
                return;
            }

            const action = button.getAttribute("data-grid-action");

            if (action === "reset") {
                toolbar.querySelector(".hotelio-grid-search").value = "";
                gridApi.setGridOption("quickFilterText", "");
                gridApi.setFilterModel(null);
                gridApi.resetColumnState();
                reloadRows();
            }

            if (action === "copy") {
                copyGrid(gridApi, columnDefinitions);
            }

            if (action === "csv") {
                gridApi.exportDataAsCsv({ fileName: `${filename}.csv` });
            }

            if (action === "json") {
                exportJson(gridApi, filename);
            }

            if (action === "pdf") {
                exportPdf(gridApi, columnDefinitions, filename);
            }
        });

        return toolbar;
    }

    function createGridShell(tableElement) {
        const shell = document.createElement("div");
        const gridElement = document.createElement("div");

        shell.className = "hotelio-grid-card";
        gridElement.className = "ag-theme-quartz hotelio-ag-grid";
        shell.appendChild(gridElement);
        tableElement.classList.add("hotelio-grid-source");
        tableElement.insertAdjacentElement("afterend", shell);

        return { shell: shell, gridElement: gridElement };
    }

    function buildFacade(gridApi, columnDefinitions, reloadRows) {
        const exportData = function exportData() {
            return getExportMatrix(gridApi, columnDefinitions);
        };

        return {
            draw: reloadRows,
            ajax: {
                reload: reloadRows,
            },
            buttons: {
                exportData: exportData,
            },
            button: {
                exportData: exportData,
            },
            clear: function clear() {
                gridApi.setGridOption("rowData", []);
                return this;
            },
            destroy: function destroy() {
                gridApi.destroy();
                return this;
            },
        };
    }

    function initializeGrid(tableElement, options, loadRows) {
        if (
            !tableElement ||
            gridInstances.has(tableElement) ||
            tableElement.closest(".modal")
        ) {
            return gridInstances.get(tableElement);
        }

        const columnDefinitions = normalizeColumnDefinitions(
            options,
            tableElement,
        );
        const filename = getFilename(options, tableElement);
        const elements = createGridShell(tableElement);
        const gridApi = createGrid(elements.gridElement, {
            columnDefs: columnDefinitions,
            rowData: [],
            pagination: true,
            paginationPageSize: options.pageLength || 10,
            paginationPageSizeSelector: [10, 25, 50, 100],
            animateRows: true,
            rowHeight: 56,
            suppressCellFocus: true,
            defaultColDef: {
                sortable: true,
                filter: true,
                resizable: true,
            },
            onGridReady: function onGridReady() {
                setTimeout(function sizeColumns() {
                    gridApi.sizeColumnsToFit();
                }, 0);
            },
            onFirstDataRendered: function onFirstDataRendered() {
                gridApi.sizeColumnsToFit();
            },
        });
        const reloadRows = function reloadRows() {
            return loadRows()
                .then(function setRows(rows) {
                    gridApi.setGridOption("rowData", rows);
                    setTimeout(function resizeColumns() {
                        gridApi.sizeColumnsToFit();
                    }, 0);
                    return rows;
                })
                .catch(function handleLoadError(error) {
                    console.error("Hotelio AG Grid failed to load rows", error);
                    gridApi.setGridOption("rowData", []);

                    return [];
                });
        };

        elements.shell.insertBefore(
            createToolbar(gridApi, columnDefinitions, filename, reloadRows),
            elements.gridElement,
        );

        const facade = buildFacade(gridApi, columnDefinitions, reloadRows);
        gridInstances.set(tableElement, facade);
        reloadRows();

        window.addEventListener("resize", function handleResize() {
            gridApi.sizeColumnsToFit();
        });

        return facade;
    }

    function createAjaxGrid(tableElement, options) {
        const ajaxOptions =
            typeof options.ajax === "string"
                ? { url: options.ajax, type: "GET" }
                : options.ajax || {};

        return initializeGrid(tableElement, options, function loadAjaxRows() {
            if (!ajaxOptions.url) {
                return Promise.resolve([]);
            }

            return new Promise(function resolveAjax(resolve, reject) {
                jquery.ajax({
                    url: ajaxOptions.url,
                    type: ajaxOptions.type || ajaxOptions.method || "GET",
                    dataType: "json",
                    data: Object.assign({}, ajaxOptions.data || {}, {
                        grid: "ag",
                    }),
                    success: function handleSuccess(response) {
                        resolve(extractRows(response));
                    },
                    error: function handleError(error) {
                        reject(error);
                    },
                });
            });
        });
    }

    function createStaticGrid(tableElement) {
        return initializeGrid(tableElement, {}, function loadStaticRows() {
            const columnDefinitions = normalizeColumnDefinitions(
                {},
                tableElement,
            );

            return Promise.resolve(
                parseStaticRows(tableElement, columnDefinitions),
            );
        });
    }

    jquery.fn.DataTable = function dataTable(options) {
        let firstGrid = null;

        this.each(function initializeEachTable() {
            const grid = createAjaxGrid(this, options || {});
            firstGrid = firstGrid || grid;
        });

        return firstGrid;
    };

    jquery.fn.dataTable = jquery.fn.dataTable || {};
    jquery.fn.dataTable.fileSave = function fileSave(blob, filename) {
        downloadBlob(blob, filename || "hotelio-export.json");
    };

    jquery.fn.dataTable.ext = jquery.fn.dataTable.ext || {};
    jquery.fn.dataTable.ext.errMode = "none";
    jquery.fn.dataTable.isDataTable = function isDataTable(tableElement) {
        return gridInstances.has(jquery(tableElement).get(0));
    };
    jquery.fn.dataTable.Api = function Api() {};
    jquery.fn.dataTable.version = "ag-grid-compat";
    jquery.fn.dataTableSettings = [];
    jquery.fn.dataTableExt = jquery.fn.dataTable.ext;
    jquery.fn.dataTable.defaults = {};
    jquery.fn.dataTable.render = {};
    jquery.fn.dataTable.Buttons = function Buttons() {};
    jquery.fn.dataTable.Buttons.version = "ag-grid-compat";
    jquery.fn.dataTable.Buttons.jszip = function jszip() {};
    jquery.fn.dataTable.Buttons.pdfMake = function pdfMake() {};
    jquery.fn.dataTable.Buttons.defaults = {};
    jquery.fn.dataTable.Buttons.ext = {};
    jquery.fn.dataTable.Buttons.exportData = function exportData() {
        return { header: [], body: [] };
    };
    jquery.fn.dataTable.fileSave = jquery.fn.dataTable.fileSave || downloadBlob;
    jquery.fn.dataTable.Editor = function Editor() {};
    jquery.fn.dataTable.Responsive = function Responsive() {};
    jquery.fn.dataTable.ColReorder = function ColReorder() {};
    jquery.fn.DataTable.ext = jquery.fn.dataTable.ext;
    jquery.fn.DataTable.isDataTable = jquery.fn.dataTable.isDataTable;

    document.addEventListener(
        "DOMContentLoaded",
        function initializeStaticTables() {
            const selectors = ["table.ListTable", "table.DataTable"];

            if (window.location.pathname.toLowerCase().includes("trash")) {
                selectors.push(".content-wrapper .content table");
            }

            document
                .querySelectorAll(selectors.join(","))
                .forEach(function initializeStaticTable(tableElement) {
                    if (
                        !gridInstances.has(tableElement) &&
                        !tableElement.classList.contains(
                            "hotelio-grid-source",
                        ) &&
                        !tableElement.closest(".modal") &&
                        tableElement.querySelector("thead") &&
                        tableElement.querySelector("tbody")
                    ) {
                        createStaticGrid(tableElement);
                    }
                });
        },
    );

    window.HotelioAgGrid = {
        refresh: function refresh(selector) {
            document
                .querySelectorAll(selector)
                .forEach(function refreshTable(tableElement) {
                    const grid = gridInstances.get(tableElement);

                    if (grid && typeof grid.draw === "function") {
                        grid.draw(false);
                    }
                });
        },
        convertStatic: createStaticGrid,
    };
})();
