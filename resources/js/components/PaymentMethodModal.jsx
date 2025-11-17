import React, { useRef, useState, useEffect } from "react";
import $ from "jquery"; // Asegúrate de tener jQuery disponible
import { isNumber } from "lodash";

function PaymentMethodModal({ translations, state, totalData, loadCart }) {
    const cart = state.cart;
    const inputAmountReceived = useRef(null);
    const [isVisibleCashPayment, setIsVisibleCashPayment] = useState(true);
    const [inputCashBack, setCashBack] = useState(false);
    const paymentMethodRef = useRef(null);
    const [paymentMethods, setPaymentMethods] = useState([]);
    const [paymentMethodsSelected, setPaymentMethodsSelected] = useState([]);
    const [paymentMethodsSelectedTotal, setPaymentMethodsSelectedTotal] =
        useState(0);

    const openModal = () => {
        const modalEl = $("#paymentMethodModal");

        modalEl.on("shown.bs.modal", function () {
            if (inputAmountReceived.current) {
                inputAmountReceived.current.focus();
            }
        });

        modalEl.modal("show");
    };

    const closeModal = () => {
        $("#paymentMethodModal").modal("hide");
        handleCleanPaymentMethods();
    };

    const handleCashPayment = (methodId) => {
        setIsVisibleCashPayment(false);
        const method = paymentMethods.find((p) => p.id == methodId);
        const amount = Number(inputAmountReceived.current.value);

        if (amount > method.amount) {
            Swal.fire({
                title: "Error",
                text: "El monto no puede ser mayor al disponible",
                icon: "error",
            });
            return;
        }
        const newData = {
            name: method.name,
            amount: Number(inputAmountReceived.current.value),
            amountPayed: Number(inputAmountReceived.current.value),
        };

        setPaymentMethodsSelected((prev) => ({
            ...prev,
            [methodId]: newData,
        }));
    };

    const removePaymentMethod = (methodId) => {
        const newData = paymentMethodsSelected[methodId];
        setPaymentMethodsSelected((prev) => {
            const updated = { ...prev };
            delete updated[methodId]; // ← elimina la key
            return updated;
        });
    };

    const handleCashBack = (event) => {
        if (isNaN(event.target.value)) return console.log("No es Numero");
        const cashBack = event.target.value - totalData.total;
        if (cashBack < 0) return setCashBack(false);
        setCashBack(cashBack);
    };

    const handleCleanPaymentMethods = () => {
        setIsVisibleCashPayment(false);
        paymentMethodRef.current = null;
        setCashBack(false);
        setPaymentMethodsSelected({});
        setPaymentMethodsSelectedTotal(0);
    };

    // USEEFFECT
    useEffect(() => {
        axios.get("/admin/payment-methods").then((res) => {
            setPaymentMethods(res.data);
        });
    }, []);

    useEffect(() => {
        const total = Object.values(paymentMethodsSelected).reduce(
            (acc, p) => acc + Number(p.amount),
            0
        );

        inputAmountReceived.current.focus();
        inputAmountReceived.current.value = "";

        setPaymentMethodsSelectedTotal(total);

        // Si no existe efectivo, no hacemos nada
        if (!paymentMethodsSelected[1]) return;

        // 1. Total sin efectivo
        const totalWithoutCash = Object.entries(paymentMethodsSelected)
            .filter(([key]) => key !== "1")
            .reduce((acc, [, p]) => acc + Number(p.amount), 0);

        // 2. Calcular nuevo amountPayed
        let cashAmountPayed = totalData.total - totalWithoutCash;
        if (cashAmountPayed < 0) cashAmountPayed = 0;

        console.log(cashAmountPayed);

        // 3. Evitar loop: solo actualizar si CAMBIÓ
        if (paymentMethodsSelected[1].amountPayed !== cashAmountPayed) {
            setPaymentMethodsSelected((prev) => ({
                ...prev,
                1: {
                    ...prev[1],
                    amountPayed: cashAmountPayed,
                },
            }));
            console.log(paymentMethodsSelected);
        }
    }, [paymentMethodsSelected]);

    const handleClickSubmit = () => {
        console.log(paymentMethodsSelected);
        axios
            .post("/admin/orders", {
                customer_id: state.customer_id,
                amount: paymentMethodsSelectedTotal,
                payment_methods: paymentMethodsSelected,
            })
            .then((res) => {
                console.log(res);
                loadCart();
                closeModal();
            })
            .catch((err) => {
                console.log(err);
                // Swal.showValidationMessage(err.response.data.message);
            });
    };

    return (
        <div className="col">
            <button
                type="button"
                className="btn btn-primary btn-block"
                disabled={!cart.length}
                onClick={openModal}
            >
                {translations.checkout}
            </button>

            <div
                className="modal fade"
                id="paymentMethodModal"
                tabIndex="-1"
                role="dialog"
                data-backdrop="static"
            >
                <div className="modal-dialog modal-lg" role="document">
                    <div className="modal-content">
                        <div className="modal-header">
                            <h5 className="modal-title">
                                {translations.checkout}
                            </h5>
                            <button
                                type="button"
                                className="close"
                                onClick={closeModal}
                            >
                                <span>&times;</span>
                            </button>
                        </div>
                        <div className="col-12 d-flex p-2 h-100">
                            <div className="col-6">
                                <div className="card h-100 p-2 d-flex flex-column justify-content-between">
                                    <div className="d-flex flex-column">
                                        <h5 className="card-title">
                                            Detalle de Compra
                                        </h5>
                                        <div className="">
                                            <div className="d-flex justify-content-between">
                                                <label className="m-0">
                                                    {translations.sub_total}:
                                                </label>
                                                <label className="m-0">
                                                    {totalData.subtotal.toFixed(
                                                        2
                                                    )}
                                                </label>
                                            </div>
                                            <div className="d-flex justify-content-between">
                                                <label className="m-0">
                                                    {translations.tax}(
                                                    {window.APP.tax_percentage}
                                                    %):
                                                </label>
                                                <label className="m-0">
                                                    {totalData.tax.toFixed(2)}
                                                </label>
                                            </div>
                                            <hr />
                                            {Object.entries(
                                                paymentMethodsSelected
                                            ).map(([key, p]) => (
                                                <div
                                                    key={key}
                                                    className="d-flex justify-content-between"
                                                >
                                                    <div className="d-flex align-items-center ">
                                                        <label
                                                            onClick={() =>
                                                                removePaymentMethod(
                                                                    key
                                                                )
                                                            }
                                                            className="m-0 text-danger pr-2"
                                                        >
                                                            <i className="fa-solid fa-xmark"></i>
                                                        </label>
                                                        <label className="m-0">
                                                            {p.name}:
                                                        </label>
                                                    </div>
                                                    <label className="m-0">
                                                        {p.amount.toFixed(2)}
                                                    </label>
                                                </div>
                                            ))}
                                        </div>
                                    </div>
                                    <div>
                                        <hr />
                                        <div className="d-flex justify-content-between">
                                            <label className="m-0">
                                                {" "}
                                                {translations.total}:
                                            </label>
                                            <label className="m-0">
                                                {" "}
                                                {paymentMethodsSelectedTotal.toFixed(
                                                    2
                                                )}
                                            </label>
                                        </div>
                                        <div className="d-flex justify-content-between">
                                            <label className="m-0">
                                                {translations.balance}:
                                            </label>
                                            <label className="m-0">
                                                {window.APP.currency_symbol}{" "}
                                                {(
                                                    totalData.total -
                                                    paymentMethodsSelectedTotal
                                                ).toFixed(2)}
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div className="col-6 d-flex flex-column justify-content-center">
                                {/* Payment Method */}
                                <div className="d-flex justify-content-between align-items-center">
                                    <label htmlFor="">
                                        {translations.amountReceived}
                                    </label>
                                    <input
                                        type="text"
                                        className=" form-control text-center col-4"
                                        placeholder={totalData.total.toFixed(2)}
                                        ref={inputAmountReceived}
                                        onChange={handleCashBack}
                                    />
                                </div>

                                <div className="d-flex justify-content-center flex-wrap">
                                    {paymentMethods.map((p) => (
                                        <button
                                            key={p.id}
                                            type="button"
                                            className={`btn btn-${
                                                paymentMethodRef.current ===
                                                p.id
                                                    ? "primary"
                                                    : "outline-primary"
                                            } btn-custom p-2 m-1 d-flex flex-column align-items-center`}
                                            onClick={() =>
                                                handleCashPayment(p.id)
                                            }
                                        >
                                            <i
                                                className={`fas ${p.icon} fa-2x mb-1`}
                                            ></i>
                                            {p.description}
                                        </button>
                                    ))}
                                </div>
                            </div>
                        </div>
                        <div className="modal-footer">
                            <button
                                type="button"
                                className="btn btn-secondary"
                                onClick={closeModal}
                            >
                                {translations.cancel}
                            </button>
                            <button
                                type="button"
                                className="btn btn-primary"
                                onClick={handleClickSubmit}
                            >
                                {translations.confirm_pay}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}

export default PaymentMethodModal;
