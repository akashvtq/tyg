const exchangeRate = 1.35; // Just a sample conversion rate

function updateTotal() {
    let total = 0;
    $("#services-list .service-item").each(function () {
        const qty = parseInt($(this).find(".qty-input").val());
        const price = parseFloat($(this).data("price"));
        total += qty * price;
    });
    $("#deposit-amount").val(total);
    $("#usd-total").text((total * exchangeRate).toFixed(2));
}

function renderProjects(serviceId, label, qty) {
    let container = $("#project-sections").find(`[data-id="${serviceId}"]`);
    if (!container.length && qty > 0) {
        container = $(`
        <div class="card p-3 mt-3" data-id="${serviceId}">
          <h5>${label}</h5>
          <div class="project-list"></div>
        </div>
      `);
        $("#project-sections").append(container);
    }

    const list = container.find(".project-list");
    list.empty();
    for (let i = 0; i < qty; i++) {
        list.append(`
        <div class="project-entry">
          <div class="mb-2 fw-bold">Project ${i + 1}</div>
          <div class="row">
            <div class="col-md-6">
              <label class="anchor-text">Suggested Anchor Text (Not Guaranteed)</label>
              <input type="text" class="form-control form-control-sm" />
            </div>
            <div class="col-md-5">
              <label class="anchor-text">Target URL *</label>
              <input type="text" class="form-control form-control-sm" />
            </div>
            <div class="col-md-1 d-flex align-items-end">
              <span class="delete-entry" title="Delete Row">🗑️</span>
            </div>
          </div>
        </div>
      `);
    }

    if (qty === 0) container.remove();
}

$(document).ready(function () {
    $(".plus-btn").on("click", function () {
        const parent = $(this).closest(".service-item");
        const input = parent.find(".qty-input");
        let qty = parseInt(input.val());
        qty++;
        input.val(qty);

        updateTotal();
        const id = parent.data("id");
        const label = parent.find("span:first").text();
        renderProjects(id, label, qty);
    });

    $(".minus-btn").on("click", function () {
        const parent = $(this).closest(".service-item");
        const input = parent.find(".qty-input");
        let qty = parseInt(input.val());
        if (qty > 0) {
            qty--;
            input.val(qty);
            updateTotal();

            const id = parent.data("id");
            const label = parent.find("span:first").text();
            renderProjects(id, label, qty);
        }
    });

    $(document).on("click", ".delete-entry", function () {
        const entry = $(this).closest(".project-entry");
        const card = entry.closest(".card");
        const serviceId = card.data("id");

        // Remove project box
        entry.remove();

        // Reduce count in the service listing
        const serviceBox = $(
            `#services-list .service-item[data-id="${serviceId}"]`
        );
        const input = serviceBox.find(".qty-input");
        let qty = parseInt(input.val());
        qty = Math.max(0, qty - 1);
        input.val(qty);

        // Re-render
        const label = serviceBox.find("span:first").text();
        renderProjects(serviceId, label, qty);
        updateTotal();
    });
});
