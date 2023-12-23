<!DOCTYPE html>
<html>
<head>
    <title></title>
</head>
<body>

<div class="col">
    <div class="mb-3">
        <label for="tgl_jam_mulai" class="form-label">Tanggal & Jam Mulai</label>
        <input type="text" name="tgl_jam_mulai" class="form-control" id="tgl_jam_mulai" placeholder="Silahkan Klik Disini" required>
    </div>
</div>

<div class="col">
    <div class="mb-3">
        <label for="lama_main" class="form-label">Lama main (Jam)</label>
        <div class="input-group">
            <button class="btn btn-outline-secondary" type="button" id="btnDecrement">-</button>
            <input type="text" name="lama_main" class="form-control" id="lama_main" value="1" readonly>
            <button class="btn btn-outline-secondary" type="button" id="btnIncrement">+</button>
        </div>
    </div>
</div>

<script>
    flatpickr("#tgl_jam_mulai", {
        enableTime: true,
        minTime: "07:00",
        maxTime: "18:00",
        dateFormat: "Y-m-d H:i",
        time_24hr: true,
        minuteIncrement: 60,
        onClose: function (selectedDates, dateStr, instance) {
            const selectedTime = instance.selectedDates[0].getHours();
            const maxDuration = 19 - selectedTime;

            const lamaMainInput = document.getElementById("lama_main");
            lamaMainInput.value = 1;
            lamaMainInput.setAttribute("max", maxDuration);
            lamaMainInput.removeAttribute("readonly");
        },
    });

    document.getElementById("btnIncrement").addEventListener("click", function () {
        updateLamaMain(1);
    });

    document.getElementById("btnDecrement").addEventListener("click", function () {
        updateLamaMain(-1);
    });

    function updateLamaMain(value) {
        const lamaMainInput = document.getElementById("lama_main");
        const currentValue = parseInt(lamaMainInput.value, 10);
        const maxDuration = parseInt(lamaMainInput.getAttribute("max"), 10);

        const newValue = currentValue + value;

        if (newValue >= 1 && newValue <= maxDuration) {
            lamaMainInput.value = newValue;
        }
    }
</script>

</body>
</html>
