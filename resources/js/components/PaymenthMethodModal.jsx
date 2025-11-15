import React, { useRef, useState, useEffect } from "react";
import $ from "jquery"; // Asegúrate de tener jQuery disponible
import { isNumber } from "lodash";

function PaymenthMethodModal({ translations, cart, totalData }) {
    console.log(totalData);
    console.log(translations);
    const inputAmountReceived = useRef(null);
    const [isVisibleCashPayment, setIsVisibleCashPayment] = useState(false);
    const [inputCashBack, setCashBack] = useState(false);
    const paymenthMethodRef = useRef(null);
    const openModal = () => {
        $("#paymentMethodModal").modal("show");
    };

    const closeModal = () => {
        $("#paymentMethodModal").modal("hide");
        setIsVisibleCashPayment(false);
        paymenthMethodRef.current = null;
        setCashBack(false);
    };

    const handleCashPayment = (method) => {
        setIsVisibleCashPayment(false);
        switch (method) {
            case 1:
                setIsVisibleCashPayment(true);
                paymenthMethodRef.current = 1;
                break;
            default:
                break;
        }
    };

    const handleCashBack = (event) => {
        if (isNaN(event.target.value)) return console.log("No es Numero");
        const cashBack = event.target.value - totalData.total;
        if (cashBack < 0) return setCashBack(false);
        setCashBack(cashBack);
    };

    // Efecto para enfocar solo cuando se muestra el input
    useEffect(() => {
        if (isVisibleCashPayment && inputAmountReceived.current) {
            inputAmountReceived.current.focus();
        }
    }, [isVisibleCashPayment]); // Se ejecuta cada vez que cambia isVisibleCashPayment

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
                                        </div>
                                    </div>
                                    <div>
                                        <hr />
                                        <div className="d-flex justify-content-between">
                                            <label className="m-0">
                                                {translations.total}:
                                            </label>
                                            <label className="m-0">
                                                {window.APP.currency_symbol}{" "}
                                                {totalData.total.toFixed(2)}
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                {/* <label className="text-lg text-center mt-2">
                                    {translations.total + " : "}
                                    {window.APP.currency_symbol}
                                    {totalData.total.toFixed(2)}
                                </label>
                                {isVisibleCashPayment && (
                                    <div className="text-center d-flex flex-column align-items-center">
                                        <input
                                            type="text"
                                            className=" form-control text-center text-lg"
                                            placeholder={
                                                translations.amountReceived
                                            }
                                            ref={inputAmountReceived}
                                            onChange={handleCashBack}
                                        />

                                        {inputCashBack && (
                                            <div className="d-flex flex-column">
                                                <label className="text-lg">
                                                    {translations.cashBack +
                                                        " : "}
                                                    {window.APP.currency_symbol}
                                                    {inputCashBack.toFixed(2)}
                                                </label>
                                                <button
                                                    type="button"
                                                    className="btn btn-success text-lg"
                                                >
                                                    Pagar
                                                </button>
                                            </div>
                                        )}
                                    </div>
                                )} */}
                            </div>
                            <div className="col-6 d-flex justify-content-center">
                                {/* Payment Method */}
                                <div className="d-flex justify-content-center flex-wrap">
                                    <button
                                        type="button"
                                        className={`btn btn-${
                                            paymenthMethodRef.current === 1
                                                ? "primary"
                                                : "outline-primary"
                                        } btn-custom p-2 m-1 d-flex flex-column align-items-center`}
                                        onClick={() => handleCashPayment(1)}
                                    >
                                        <i className="fas fa-money-bill-wave fa-2x mb-1"></i>
                                        Efectivo
                                    </button>
                                    <button
                                        type="button"
                                        className="btn btn-outline-primary btn-custom p-2 m-1 d-flex flex-column align-items-center"
                                    >
                                        <i className="fas fa-key fa-2x mb-1"></i>
                                        Clave
                                    </button>
                                    <button
                                        type="button"
                                        className="btn btn-outline-primary btn-custom p-2 m-1 d-flex flex-column align-items-center"
                                    >
                                        <i className="fab fa-cc-visa fa-2x mb-1"></i>{" "}
                                        Tarjeta
                                    </button>
                                    <button
                                        type="button"
                                        className="btn btn-outline-primary btn-custom p-2 m-1 d-flex flex-column align-items-center"
                                    >
                                        <i className="fas fa-mobile-alt fa-2x mb-1"></i>
                                        Yappy
                                    </button>
                                    <button
                                        type="button"
                                        className="btn btn-outline-primary btn-custom p-2 m-1 d-flex flex-column align-items-center"
                                    >
                                        <i className="fas fa-exchange-alt fa-2x mb-1"></i>{" "}
                                        Transferencia
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}

export default PaymenthMethodModal;
