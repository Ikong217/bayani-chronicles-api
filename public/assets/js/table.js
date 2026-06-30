function dataTablesInit() {
  $(".table").each(function (index, table) {
    $(table).DataTable({
      dom: "Bflrtip",
      ordering: false,
      buttons: [
        {
          extend: "copyHtml5",
          text: '<i class="bi bi-clipboard fs-5"></i>',
          titleAttr: "Copy",
          className: "btn btn-sm btn-outline-danger border-0 shadow",
        },
        {
          extend: "excelHtml5",
          text: '<i class="bi bi-filetype-xlsx fs-5"></i>',
          titleAttr: "excel",
          className: "btn btn-sm btn-outline-danger border-0 shadow",
        },
        {
          extend: "csvHtml5",
          text: '<i class="bi bi-filetype-csv fs-5"></i>',
          titleAttr: "CSV",
          className: "btn btn-sm btn-outline-danger border-0 shadow",
        },
        {
          extend: "pdfHtml5",
          text: '<i class="bi bi-filetype-pdf fs-5"></i>',
          titleAttr: "CSV",
          className: "btn btn-sm btn-outline-danger border-0 shadow",
        },
        {
          extend: "print",
          text: '<i class="bi bi-printer fs-5"></i>',
          titleAttr: "print",
          className: "btn btn-sm btn-outline-danger border-0 shadow",
        },
      ],
      pageLength: 5,
      lengthMenu: [
        [5, 10, 25, 50, -1],
        [5, 10, 25, 50, "All"],
      ],
      initComplete: function () {
        $(".dt-buttons button").removeClass("dt-button").addClass("p-3 border");
        $(".dt-buttons").addClass(
          "w-50 mb-2 d-flex justify-content-start align-items-center gap-2"
        );
        $(".dataTables_filter").addClass("float-start w-25");
        $(".dataTables_filter input").addClass("form-control shadow-none");
        $(".dataTables_filter label").addClass(
          "d-flex justify-content-center align-items-center gap-2 "
        );
        $(".dataTables_length").addClass("float-end small");
      },
    });
  });
}

dataTablesInit();
