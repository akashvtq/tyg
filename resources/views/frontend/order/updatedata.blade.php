<script>
    let order_summary = '{!! json_encode($order_summary) !!}';
    if (order_summary != null) {
        order_summary = JSON.parse(order_summary);
        let local_storage = JSON.parse(localStorage.getItem("order_summary")) || {};
        order_summary = {
            ...order_summary,
            ...local_storage
        };
    }

    const exchangeRate = 1.35; // Just a sample conversion rate

    function updateTotal() {
        let total = 0;
        $("#services-list .service-item").each(function() {
            const qty = parseInt($(this).find(".qty-input").val());
            const price = parseFloat($(this).data("price"));
            total += qty * price;
        });
        $("#deposit-amount").val(total);
        $("#usd-total").text((total * exchangeRate).toFixed(2));
        $('#deposit-method-select').change();
    }

    function renderProjects(serviceId, label, qty, anchor_text = "", target_url = "") {
        let container = $("#project-sections").find(`[data-id="${serviceId}"]`);
        if (!container.length && qty > 0) {
            container = $(`
        <div class="card p-3 mt-3" data-id="${serviceId}">
          <h5 class="text-dark mb-2">${label}</h5>
          <div class="project-list"></div>
          <button type="button" class="btn btn-dark w-100" onclick="$('#qty-${serviceId}').find('.plus-btn').click();">Add Row</button>
        </div>
      `);
            $("#project-sections").append(container);
        }
        const list = container.find(".project-list");
        list.empty();
        let project_array = {};
        let existing_projects = order_summary[serviceId]['projects'];
        for (let i = 0; i < qty; i++) {
            var project_id = i + 1;
            if (existing_projects["project_" + project_id] === undefined) {
                project_array["project_" + project_id] = {
                    "anchor_text": anchor_text,
                    "name": "Project " + project_id,
                    "project_id": "project_" + project_id,
                    "target_url": target_url
                }
            } else {
                project_array["project_" + project_id] = existing_projects["project_" + project_id];
            }

            list.append(`
                <div class="project-entry" id="${"project_" + project_id}">
                <div class="mb-2 fw-bold">Project ${project_id}</div>
                <div class="row">
                    <div class="col-md-6">
                    <label class="anchor-text">Suggested Anchor Text (Not Guaranteed)</label>
                    <input type="text" class="form-control form-control-sm project_anchor_text" data-service="${serviceId}" value="${project_array["project_" + project_id]["anchor_text"]}"/>
                    </div>
                    <div class="col-md-5">
                    <label class="anchor-text">Target URL *</label>
                    <input type="text" class="form-control form-control-sm project_target_url" data-service="${serviceId}" value="${project_array["project_" + project_id]["target_url"]}"/>
                    </div>
                    <div class="col-md-1 d-flex align-items-end">
                    <button type="button" class="btn btn-sm btn-dark duplicate-btn" data-slug="${serviceId}">+</button>
                    <span class="delete-entry" title="Delete Row">🗑️</span>
                    </div>
                </div>
                </div>
            `);
        }
        order_summary[serviceId]['projects'] = project_array;
        order_summary[serviceId]['value'] = qty;
        if (qty === 0) container.remove();
        localStorage.setItem("order_summary", JSON.stringify(order_summary));
    }

    $(document).on("blur", ".project_anchor_text,.project_target_url", function() {
        let project_id = $(this).parents(".project-entry").attr("id");
        let existing_projects = order_summary[$(this).data("service")]['projects'];

        if ($(this).hasClass('project_anchor_text'))
            existing_projects[project_id]["anchor_text"] = $(this).val();
        if ($(this).hasClass('project_target_url'))
            existing_projects[project_id]["target_url"] = $(this).val();

        order_summary[$(this).data("service")]['projects'] = existing_projects;
        localStorage.setItem("order_summary", JSON.stringify(order_summary));
    });

    $(document).ready(function() {
        for (let o in order_summary) {
            $("#services-list").find('div[data-id="' + o + '"]').find(".qty-input").val(order_summary[o].value);
            renderProjects(o, order_summary[o].name, order_summary[o].value);
        }
        updateTotal();
        $("#deposit-method-select").val(localStorage.getItem("deposit_method") || "0").change();
        $("#order_notes").val(localStorage.getItem("order_notes") || "");

        $(".plus-btn").on("click", function() {
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

        $(".minus-btn").on("click", function() {
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

        $(document).on("click", ".duplicate-btn", function() {
            const parent = $('.service-item[data-id="' + $(this).data('slug') + '"]');
            const input = parent.find(".qty-input");
            let qty = parseInt(input.val());
            qty++;
            input.val(qty);

            updateTotal();
            const id = parent.data("id");
            const label = parent.find("span:first").text();
            renderProjects(id, label, qty, $(this).parents(".project-entry").find(".project_anchor_text").val(), $(this).parents(".project-entry").find(".project_target_url").val());
        });
        $(document).on("click", ".delete-entry", function() {
            const entry = $(this).closest(".project-entry");
            const card = entry.closest(".card");
            const serviceId = card.data("id");

            // Remove project box
            entry.remove();

            // Reduce count in the service listing
            const serviceBox = $(
                `#services-list .service-item[data-id="${serviceId}"]`);
            const input = serviceBox.find(".qty-input");
            let qty = parseInt(input.val());
            qty = Math.max(0, qty - 1);
            input.val(qty);

            // Re-render
            const label = serviceBox.find("span:first").text();
            renderProjects(serviceId, label, qty);
            updateTotal();
        });
        $(document).on("keyup", "#order_notes", function() {
            localStorage.setItem("order_notes", $(this).val());
        });
        $("#process_to_pay").on("click", function(e) {
            e.preventDefault();
            localStorage.setItem("order_summary", JSON.stringify(order_summary));
            let form = $('#form-order');
            $.ajax({
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $("input[name='_token']").val()
                },
                url: $(form).attr('action'),
                data: {
                    notes: $("#order_notes").val(),
                    order_summary: JSON.stringify(order_summary),
                    payment_method: $("#deposit-method-select").val()
                },
                success: function(response) {
                    if (response.success) {
                        notifyEvs('success', response.message);
                        localStorage.removeItem("order_summary");
                        localStorage.removeItem("deposit_method");
                        localStorage.removeItem("order_notes");

                        setTimeout(function() {
                             window.location.href = response.redirecturl;
                        }, 3000);
                    } else {
                        notifyEvs('error', response.message);
                    }
                },
                error: function(xhr) {
                    notifyEvs('error', xhr.responseText);
                }
            });
        });
    });
</script>