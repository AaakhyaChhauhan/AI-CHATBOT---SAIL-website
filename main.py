import streamlit as st
import pandas as pd
import pymysql
from prophet import Prophet
import plotly.graph_objs as go

# MySQL DB credentials
DB_HOST = "sql12.freesqldatabase.com"
DB_USER = "sql12787890"
DB_PASS = "ZMLhL9LGUN"
DB_NAME = "sql12787890"

# Load invoice data
@st.cache_data
def load_data():
    conn = pymysql.connect(
        host=DB_HOST,
        user=DB_USER,
        password=DB_PASS,
        database=DB_NAME,
        port=3306
    )
    query = "SELECT created_at, amount FROM invoices ORDER BY created_at"
    df = pd.read_sql(query, conn)
    conn.close()
    df['created_at'] = pd.to_datetime(df['created_at'])
    df = df.rename(columns={'created_at': 'ds', 'amount': 'y'})
    return df

# Forecast with Prophet
def forecast_sales(df):
    model = Prophet()
    model.fit(df)
    future = model.make_future_dataframe(periods=30)
    forecast = model.predict(future)
    return forecast

# Streamlit UI
st.set_page_config(page_title="VendorBot AI Assistant", layout="wide")
st.title("🤖 AI Chatbot for Sales Forecast")

user_input = st.text_input("Ask something about sales...", "")

if user_input:
    if "predict" in user_input.lower() or "future" in user_input.lower():
        df = load_data()
        forecast = forecast_sales(df)
        st.subheader("📈 30-Day Sales Forecast")
        fig = go.Figure()
        fig.add_trace(go.Scatter(x=forecast['ds'], y=forecast['yhat'], name="Forecast"))
        fig.add_trace(go.Scatter(x=df['ds'], y=df['y'], name="Actual Sales"))
        st.plotly_chart(fig, use_container_width=True)
    else:
        st.warning("I can help with forecasting. Try asking something like 'predict future sales'.")
